<?php

namespace App\Services;

use Exception;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostService extends Service {

    private $perPage;
	/**
    * PostService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
    }

    public function create(int $userId, Request $data): JsonResponse
    {
        try{
            $post = Post::create([
                'user_id' => $userId,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => 'draft',
                'location' => $data['location'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => $data['country'],
                'tags' => $data['tags'] ?? [],
            ]);

            return $this->jsonSuccess(201, 'Post created successfully!', ['post' => new PostResource($post)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function publish(int $user_id, int $id): JsonResponse
    {
        try{
            $is_updated = Post::where('user_id', $user_id)->where('id', $id)
            ->update([
                'status' => 'published',
            ]);

            if( $is_updated ) {
                return $this->jsonSuccess(200, 'Post published successfully!', []);
            } else {
                return $this->jsonError(403, 'Post publishing failed!', []);
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $userId, int $id): JsonResponse
    {
        try{
            $album = Album::where('id', $id)->where('user_id', $userId)->statusNot('deleted')->first();
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
            ->statusNot('deleted');

            return $this->jsonSuccess(200, 'Success', ['albums' => AlbumResource::collection($albums->paginate($this->perPage))->resource]);
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
                return $this->jsonSuccess(204, 'Post Deleted successfully');
            } else {
                return $this->jsonError(403, 'No post found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
