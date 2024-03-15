<?php

namespace App\Services;

use App\Http\Resources\ItineraryResource;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NameResource;
use App\Http\Resources\PostResource;
use App\Models\Album;
use App\Models\CollabItin;
use App\Models\Itinerary;
use App\Models\ItinPost;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ItinService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * AlbumService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function create(int $userId, Request $data): JsonResponse
    {
        try{
            $album = Album::where('id', $data['album_id'])->where('user_id', $userId)->first();
            if($album) {
                $itinerary = Itinerary::create([
                    'user_id' => $userId,
                    'album_id' => $data['album_id'],
                    'name' => $data['name'],
                    'is_collaborative' => $data['is_collaborative'],
                    'data' => $data['data'],
                    'status' => 'published'
                ]);

                $itinerary->posts()->attach($data['post_ids']);

                if($data['is_collaborative'] == true) {
                    $itinerary->collaborators()->attach($data['user_ids']);
                    $album->collaborators()->attach($data['user_ids']);
                    Album::where('id', $album->id)->update(['is_collaborative' => true]);
                }

                return $this->jsonSuccess(201, 'Itinerary created successfully!', ['itinerary' => new NameResource($itinerary)]);
            } else {
                return $this->jsonError(403, 'No Album found to create Itinerary');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $userId, int $id): JsonResponse
    {
        try{
            // get posts of itins where either user owns it or it's collaborative with that user
            $posts = Post::whereHas('itins', function (Builder $query) use ($userId, $id) {
                    $query->where($query->qualifyColumn('id'), $id)
                    ->where($query->qualifyColumn('status'), 'published')
                    ->where(function(Builder $query) use ($userId, $id) {
                        // user owner condition
                        $query->where($query->qualifyColumn('user_id'), $userId)
                        // collaboration condition
                        ->orWhere(function(Builder $query) use ($userId, $id) {
                            $query->where($query->qualifyColumn('is_collaborative'), 1)
                            ->whereHas('collabItins', function(Builder $query) use ($userId, $id) {
                                $query->where($query->qualifyColumn('user_id'), $userId)
                                ->where($query->qualifyColumn('itin_id'), $id);
                            });
                        });
                    });
                })
                ->with('media')
                ->status('published')->get();

            return $this->jsonSuccess(200, 'Success', ['posts' => PostResource::collection($posts)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list($userId): JsonResponse
    {
        try{
            // get itins where either user owns it or it's collaborative with that user
            $itineraries = Itinerary::query();
            $itineraries->whereLike('name', request()->name)
            ->where(function(Builder $query) use ($userId) {
                // user owner condition
                $query->where($query->qualifyColumn('user_id'), $userId)
                // collaboration condition
                ->orWhere(function(Builder $query) use ($userId) {
                    $query->where($query->qualifyColumn('is_collaborative'), 1)
                    ->whereHas('collabItins', function(Builder $query) use ($userId) {
                        $query->where($query->qualifyColumn('user_id'), $userId);
                    });
                });
            })
            ->when(request()->album_id, function (Builder $query) {
                $query->where('album_id', request()->album_id);
            })
            ->whereHas('album', function (Builder $query) {
                $query->where($query->qualifyColumn('status'), '!=', 'deleted');
            })
            ->withCount('posts', 'collaborators')
            ->with('user', 'collaborators')
            ->orderBy($this->orderBy, $this->orderIn)
            ->statusNot('deleted');

            $itineraries = $itineraries->paginate($this->perPage);

            $updatedItinerary = $itineraries->getCollection()->map(function($itinerary) use ($userId) {
                if ($itinerary->user_id != $userId) {
                    $itinerary->via_collab = true;
                } else {
                    $itinerary->via_collab = false;
                }
                return $itinerary;
            });

            $itineraries->setCollection($updatedItinerary);

            return $this->jsonSuccess(200, 'Success', ['itineraries' => ItineraryResource::collection($itineraries)->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function update(int $userId, int $id, Request $data): JsonResponse
    {
        try{
            $itinerary = Itinerary::where('id', $id)->statusNot(['deleted'])->first();

            $isCollaborator = CollabItin::where('itin_id', $id)->where('user_id', $userId)->first();

            if($itinerary && ($itinerary->user_id == $userId || $isCollaborator)) {

                $fields = [
                    'name' => $data['name'],
                    'data' => $data['data'],
                ];

                if( !$isCollaborator ) {
                    $fields['is_collaborative'] = $data['is_collaborative'];

                    if($data['is_collaborative'] == true) {
                        // sync with itinsCollab
                        $itinerary->collaborators()->sync($data['user_ids']);

                        // attach to album as well if collaborators are added
                        $album = Album::where('id', $itinerary->album_id)->first();
                        if($album) {
                            $album->collaborators()->attach($data['user_ids']);
                            Album::where('id', $album->id)->update(['is_collaborative' => true]);
                        }
                    } else {
                        CollabItin::where("itin_id", $id)->delete();
                    }
                }

                $isUpdated = Itinerary::where('id', $id)->update($fields);

                ItinPost::where("itin_id", $id)->delete();
                $itinerary->posts()->attach($data['post_ids']);

                return $this->jsonSuccess(200, 'Updated Successfully', ['itinerary' => new NameResource(Itinerary::find($id))]);
            } else {
                return $this->jsonError(403, 'No itinerary found to udpate');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $itinerary = Itinerary::where('id', $id)->statusNot(['deleted'])->first();

            $isCollaborator = CollabItin::where('itin_id', $id)->where('user_id', $userId)->first();

            if($itinerary && ($itinerary->user_id == $userId || $isCollaborator)) {
                if( $isCollaborator ) {
                    $isCollaborator->delete();
                } else {
                    Itinerary::where('id', $id)->update(['status' => 'deleted']);
                }
                return $this->jsonSuccess(204, 'Itinerary Deleted successfully');
            } else {
                return $this->jsonError(403, 'No Itinerary found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
