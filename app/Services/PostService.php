<?php

namespace App\Services;

use Exception;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use App\Models\Album;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * PostService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
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
            ->statusNot(['archived', 'deleted'])
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

    public function get(int $id): JsonResponse
    {
        try{
            $post = Post::where('id', $id)->with('user')->status('published')->first();
            return $this->jsonSuccess(200, 'Success', ['post' => $post ? new PostResource($post) : []]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(): JsonResponse
    {
        try{
            $posts = Post::query();
            $posts->when(request()->user_id, function (Builder $query) {
                $query->where('user_id', request()->user_id);
            })
            ->when(request()->title, function (Builder $query) {
                $query->whereLike('title', request()->title);
            })
            ->when(request()->description, function (Builder $query) {
                $query->whereLike('description', request()->description);
            })
            ->when(request()->city, function (Builder $query) {
                $query->whereLike('city', request()->city);
            })
            ->when(request()->state, function (Builder $query) {
                $query->whereLike('state', request()->state);
            })
            ->when(request()->country, function (Builder $query) {
                $query->whereLike('country', request()->country);
            })
            ->when(request()->tags, function (Builder $query) {
                $query->whereLike('tags', '"' . request()->tags . '"');
            })
            ->status('published')
            ->with('user')
            ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['posts' => PostResource::collection($posts->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function update(int $userId, int $id, array $data): JsonResponse
    {
        try{
            $isUpdated = Post::where('id', $id)->where('user_id', $userId)
                ->statusNot(['archived', 'deleted'])
                ->update($data);
            if( $isUpdated ) {
                return $this->jsonSuccess(200, 'Updated Successfully', ['post' => new PostResource(Post::find($id))]);
            } else {
                return $this->jsonError(403, 'No post found to udpate');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $is_deleted = Post::where('id', $id)->where('user_id', $userId)
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

    public function attachCategory(int $userId, int $id): JsonResponse
    {
        try{
            $post = Post::where('id', $id)->where('user_id', $userId)
                ->statusNot(['archived', 'deleted'])->first();
            if( $post ) {
                $post->categories()->sync(request()->category_ids);
                return $this->jsonSuccess(200, 'Categories attached!');
            } else {
                return $this->jsonError(403, 'No post found to attach category');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function bindAlbum(int $userId, int $id): JsonResponse
    {
        try{
            $album = Album::where('id', request()->album_id)->where('user_id', $userId)
                ->statusNot(['deleted'])->exists();

            if($album || (!request()->album_id) ) {
                $is_updated = Post::where('id', $id)->where('user_id', $userId)
                    ->statusNot(['archived', 'deleted'])
                    ->update([
                        'album_id' => request()->album_id,
                ]);

                if( $is_updated ) {
                    return $this->jsonSuccess(200, 'Post updated!');
                } else {
                    return $this->jsonError(403, 'No post found');
                }
            } else {
                return $this->jsonError(403, 'Album not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
