<?php

namespace App\Services;

use Exception;
use App\Models\Album;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NameResource;
use App\Http\Resources\AlbumResource;
use App\Models\Activities;
use App\Models\AlbumPost;
use App\Models\CollabAlbum;
use App\Models\CollabItin;
use App\Models\Itinerary;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class AlbumService extends Service {

    private $perPage, $orderBy, $orderIn;
    /**
	* @var activityService
	*/
	private $activityService;

	/**
     * PostService Constructor
     * @param ActivityService
    */
    public function __construct(ActivityService $activityService) {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
        $this->activityService = $activityService;
    }

    public function create(int $userId, array $data): JsonResponse
    {
        try{
            $album = Album::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'is_collaborative' => $data['is_collaborative'],
                'status' => 'published'
            ]);

            if($data['is_collaborative'] == true) {
                $album->collaborators()->attach($data['user_ids']);

                // WRITE ACTIVITY
                foreach($data['user_ids'] as $collaborId) {
                    $this->activityService->generateActivity($collaborId, $userId, 'collab_on_album', $album->id);
                }
            }

            return $this->jsonSuccess(201, 'Album created successfully!', ['album' => new NameResource($album)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $userId, int $id): JsonResponse
    {
        try{
            $album = Album::where('id', $id)
                ->where(function(Builder $query) use ($userId) {
                    // user owner condition
                    $query->where($query->qualifyColumn('user_id'), $userId)
                    // collaboration condition
                    ->orWhere(function(Builder $query) use ($userId) {
                        $query->where($query->qualifyColumn('is_collaborative'), 1)
                        ->whereHas('collabAlbums', function(Builder $query) use ($userId) {
                            $query->where($query->qualifyColumn('user_id'), $userId);
                        });
                    });
                })
                ->with('user', 'collaborators')
                ->withCount('posts', 'collaborators', 'itineraries')
                ->statusNot('deleted')->first();

            if($album) {
                if ($album->user_id != $userId) {
                    $album->via_collab = true;
                } else {
                    $album->via_collab = false;
                }
                $album->media_count = $album->media_count;
                $album->first_media = $album->first_media;
                $album->first_post = $album->first_post;
            }

            return $this->jsonSuccess(200, 'Success', ['album' => $album ? new AlbumResource($album) : []]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(int $userId): JsonResponse
    {
        try{
            $albums = Album::query();
            $albums->whereLike('name', request()->name)
            ->where(function(Builder $query) use ($userId) {
                // user owner condition
                $query->where($query->qualifyColumn('user_id'), $userId)
                // collaboration condition
                ->orWhere(function(Builder $query) use ($userId) {
                    $query->where($query->qualifyColumn('is_collaborative'), 1)
                    ->whereHas('collabAlbums', function(Builder $query) use ($userId) {
                        $query->where($query->qualifyColumn('user_id'), $userId);
                    });
                });
            })
            ->with([
                'user',
                'collaborators',
                'itineraries',
                'posts' => function ($query) {
                    $query->status('published')->select('posts.id');
                }
            ])
            ->withCount('posts', 'collaborators', 'itineraries')
            ->orderBy($this->orderBy, $this->orderIn)
            ->statusNot('deleted');

            $albums = $albums->paginate($this->perPage);

            $updatedAlbums = $albums->getCollection()->map(function($album) use ($userId) {
                if ($album->user_id != $userId) {
                    $album->via_collab = true;
                } else {
                    $album->via_collab = false;
                }
                $album->media_count = $album->media_count;
                $album->first_media = $album->first_media;
                $album->first_post = $album->first_post;
                $album->post_ids = $album->posts->pluck('id')->toArray();
                return $album;
            });

            $albums = $updatedAlbums;

            return $this->jsonSuccess(200, 'Success', ['albums' => AlbumResource::collection($albums)->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function update(int $userId, int $id, array $data): JsonResponse
    {
        try{
            $album = Album::where('id', $id)->status(['published'])->first();

            $isCollaborator = CollabAlbum::where('album_id', $id)->where('user_id', $userId)->first();

            if($album && ($album?->user_id == $userId || $isCollaborator)) {

                $fields = [
                    'name' => $data['name'],
                ];

                if( !$isCollaborator ) {
                    $fields['is_collaborative'] = $data['is_collaborative'];

                    if($data['is_collaborative'] == true) {
                        // sync with Collaborators
                        $album->collaborators()->sync($data['user_ids']);
                        // remove all the Itinerary collaboration other than received user_ids
                        CollabItin::whereNotIn('user_id', $data['user_ids'])
                        ->whereHas('itin', function(Builder $query) use ($id) {
                            $query->where($query->qualifyColumn('album_id'), $id);
                        })->delete();

                        // WRITE ACTIVITY
                        foreach($data['user_ids'] as $collaborId) {
                            $activity = Activities::where('user_id', $collaborId)
                                ->where('caused_by', $userId)
                                ->where('model_id', $album->id)
                                ->where('type', 'collab_on_album')
                                ->first();

                            if( !$activity ) {
                                $this->activityService->generateActivity($collaborId, $userId, 'collab_on_album', $album->id);
                            }
                        }

                    } else {
                        CollabAlbum::where("album_id", $id)->delete();
                        CollabItin::whereHas('itin', function(Builder $query) use ($id) {
                            $query->where($query->qualifyColumn('album_id'), $id);
                        })->delete();
                    }
                }

                Album::where('id', $album->id)->update($fields);
                return $this->jsonSuccess(200, 'Updated Successfully', ['album' => new NameResource(Album::find($id))]);
            } else {
                return $this->jsonError(403, 'No album found to udpate, or cannot update default Album');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $album = Album::where('id', $id)->statusNot(['default', 'deleted'])->first();

            $isCollaborator = CollabAlbum::where('album_id', $id)->where('user_id', $userId)->first();

            if($album && ($album?->user_id == $userId || $isCollaborator)) {
                if( $isCollaborator ) {
                    // delete collaborator
                    $isCollaborator->delete();
                    // delete itins collaboration
                    CollabItin::where('user_id', $userId)
                    ->whereHas('itin', function(Builder $query) use ($id) {
                        $query->where($query->qualifyColumn('album_id'), $id);
                    })->delete();

                } else {
                    Album::where('id', $album->id)->update(['status' => 'deleted']);
                    // delete collaborations
                    CollabAlbum::where('album_id', $id)->delete();
                    // delete itins
                    $itinsUpdate = Itinerary::where('album_id', $id)->update(['status' => 'deleted']);
                    // delete itin collaborations
                    CollabItin::whereHas('itin', function(Builder $query) use ($id) {
                        $query->where($query->qualifyColumn('album_id'), $id);
                    })->delete();
                }
                return $this->jsonSuccess(204, 'Album Deleted successfully');
            } else {
                return $this->jsonError(403, 'No album found to delete, or can\'t delete default album');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function addPost(int $userId, array $data): JsonResponse
    {
        try{
            $album = Album::where('id', $data['album_id'])->where('user_id', $userId)
                ->statusNot(['deleted'])->exists();

            $isCollaborator = CollabAlbum::where('album_id', $data['album_id'])->where('user_id', $userId)->first();

            $post = Post::where('id', $data['post_id'])->status('published')->exists();

            if( ($album || $isCollaborator) && $post ) {
                $relation = AlbumPost::firstOrCreate([
                    'album_id' => $data['album_id'],
                    'post_id' => $data['post_id'],
                ]);

                return $this->jsonSuccess(200, 'Post added to album successfully!');
            } else {
                return $this->jsonError(403, 'Album or post not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function removePost(int $userId, array $data): JsonResponse
    {
        try{
            $album = Album::where('id', $data['album_id'])->where('user_id', $userId)
                ->statusNot(['deleted'])->exists();

            $isCollaborator = CollabAlbum::where('album_id', $data['album_id'])->where('user_id', $userId)->first();

            if($album || $isCollaborator) {
                $isDeleted = AlbumPost::where('album_id', $data['album_id'])
                    ->where('post_id', $data['post_id'])->delete();

                return $this->jsonSuccess(200, 'Post removed from album successfully!');
            } else {
                return $this->jsonError(403, 'Album not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
