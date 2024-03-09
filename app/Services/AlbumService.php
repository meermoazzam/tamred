<?php

namespace App\Services;

use Exception;
use App\Models\Album;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NameResource;
use App\Http\Resources\AlbumResource;
use App\Models\AlbumPost;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class AlbumService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * AlbumService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function create(int $userId, array $data): JsonResponse
    {
        try{
            $album = Album::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'status' => 'published'
            ]);
            return $this->jsonSuccess(201, 'Album created successfully!', ['album' => new NameResource($album)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $userId, int $id): JsonResponse
    {
        try{
            $album = Album::where('id', $id)->where('user_id', $userId)
                ->with('itineraries')
                ->withCount('posts')
                ->statusNot('deleted')->first();

            if($album) {
                $album->media_count = $album->media_count;
                $album->first_media = $album->first_media;
                $album->first_post = $album->first_post;
            }

            return $this->jsonSuccess(200, 'Success', ['album' => $album ? new AlbumResource($album) : []]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(int|null $userId = null): JsonResponse
    {
        try{
            $albums = Album::query();
            $albums->when($userId, function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereLike('name', request()->name)
            ->with('itineraries')
            ->withCount('posts')
            ->orderBy($this->orderBy, $this->orderIn)
            ->statusNot('deleted');

            $albums = $albums->paginate($this->perPage);

            $updatedAlbums = $albums->getCollection()->map(function($album) {
                $album->media_count = $album->media_count;
                $album->first_media = $album->first_media;
                $album->first_post = $album->first_post;
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
            $isUpdated = Album::where('id', $id)->where('user_id', $userId)
                ->statusNot(['deleted', 'default'])
                ->update(['name' => $data['name']]);
            if( $isUpdated ) {
                return $this->jsonSuccess(200, 'Updated Successfully', ['album' => new NameResource(Album::find($id))]);
            } else {
                return $this->jsonError(403, 'No album found to udpate');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $is_deleted = Album::where('id', $id)->where('user_id', $userId)->statusNot(['default'])
                ->update(['status' => 'deleted']);
            if( $is_deleted ) {
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
            $post = Post::where('id', $data['post_id'])->status('published')->exists();

            if($album && $post) {
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

            if($album) {
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
