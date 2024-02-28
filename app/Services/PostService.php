<?php

namespace App\Services;

use Str;
use Exception;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReactionResource;
use App\Http\Resources\UserShortResource;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostService extends Service {


    private $perPage, $orderBy, $orderIn;
    /**
	* @var mediaService
	*/
	private $mediaService;

	/**
     * PostService Constructor
     * @param MediaService
    */
    public function __construct(MediaService $mediaService) {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
        $this->mediaService = $mediaService;
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
                'tags' => $data['tags'] ? array_unique($data['tags']) : [],
                'tagged_users' => $data['tagged_users'] ? array_unique($data['tagged_users']) : [],
                'allow_comments' => $data['allow_comments'] === false ? false : true,
            ]);

            return $this->jsonSuccess(201, 'Post created successfully!',
                [
                    'post' => new PostResource($post),
                ]
            );
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function publish(int $userId, int $id): JsonResponse
    {
        try{
            $is_updated = Post::where('user_id', $userId)->where('id', $id)
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
            $post = Post::where('id', $id)->with('user', 'media')->status('published')->first();

            $taggedUsersData = $post?->tagged_users != null ? $post->tagged_users : [];
            $taggedUsersData = User::whereIn('id', array_unique($taggedUsersData))->get();

            return $this->jsonSuccess(200, 'Success', [
                'post' => $post ? new PostResource($post) : [],
                'tagged_users_data' => $taggedUsersData ? UserShortResource::collection($taggedUsersData) : []
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list($userId): JsonResponse
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
            ->when(request()->album_id, function (Builder $query) use ($userId) {
                $query->whereHas('albumPosts.album', function (Builder $query) use ($userId) {
                    $query->where($query->qualifyColumn('id'), request()->album_id)
                        ->where($query->qualifyColumn('user_id'), $userId)
                        ->where($query->qualifyColumn('status'), '!=', 'deleted');
                });
            })
            ->when(request()->categories, function (Builder $query) {
                $query->whereHas('categories', function (Builder $query) {
                    $query->whereIn($query->qualifyColumn('id'), request()->categories)
                        ->orWhereIn($query->qualifyColumn('parent_id'), request()->categories);
                });
            })
            ->status('published')
            ->with('user', 'media', 'categories')
            ->orderBy($this->orderBy, $this->orderIn);

            $posts = $posts->paginate($this->perPage);

            $taggedUsersData = [];
            foreach($posts as $post) {
                $taggedUsersData = array_merge($taggedUsersData, $post->tagged_users);
            }
            $taggedUsersData = User::whereIn('id', array_unique($taggedUsersData));

            return $this->jsonSuccess(200, 'Success', [
                'posts' => PostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
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

            $isMediaDeleted = Media::where('user_id', $userId)
                ->where('mediable_id', $id)
                ->where('mediable_type', (new Post)->getMorphClass())
                ->update(['status' => 'deleted']);

            $is_deleted = Comment::where('post_id', $id)
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

    public function attachCategories(int $userId, int $id): JsonResponse
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

    public function react(int $userId, int $id): JsonResponse
    {
        try{
            // check if post exist or available to react
            $post = Post::where('id', $id)->status(['published'])->first();

            if($post) {
                if(true == request()->react) {
                    // create the reaction
                    $reaction = Reaction::firstOrCreate([
                        'user_id' => $userId,
                        'type' => request()->type,
                        'reactable_id' => $id,
                        'reactable_class' => $post->getMorphClass(),
                    ],[]);

                    // update the total likes column in posts
                    if($reaction->wasRecentlyCreated) $post->increment('total_likes');

                } else {
                    // remove the reaction
                    $is_deleted = Reaction::where('user_id', $userId)
                        ->where('type', request()->type)
                        ->where('reactable_id', $id)
                        ->where('reactable_class', $post->getMorphClass())
                        ->delete();

                    // update the total likes column in posts
                    if($is_deleted) $post->decrement('total_likes');
                }
                return $this->jsonSuccess(200, 'Reaction updated!');
            } else {
                return $this->jsonError(403, 'Post not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function reactList($id): JsonResponse
    {
        try{
            // check if post exist or available to react
            $post = Post::where('id', $id)->status(['published'])->first();

            if($post) {
                $reactions = Reaction::query();
                $reactions->when(request()->type, function (Builder $query) {
                    $query->where('type', request()->type);
                })
                ->where('reactable_id', $id)
                ->where('reactable_class', $post->getMorphClass())
                ->with('user')
                ->orderBy($this->orderBy, $this->orderIn);

                return $this->jsonSuccess(200, 'Success', ['reactions' => ReactionResource::collection($reactions->paginate($this->perPage))->resource]);
            } else {
                return $this->jsonError(403, 'Post not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function uploadMedia($userId, $request): JsonResponse
    {
        try{
            // check if post exist or available to react
            $post = Post::where('id', $request['post_id'])->where('user_id', $userId)
                ->statusNot(['deleted'])->first();

            if($post) {
                $content = $request['file'];
                $thumb = $request['thumbnail'];

                $content_slug = 'tamred/' . env('APP_ENV', 'dev') . '/media/users/' . $userId . '/post-' . $post->id . '-content-' . Str::random(10) . '.' . $content->getClientOriginalExtension();
                $thumb_slug = 'tamred/' . env('APP_ENV', 'dev') . '/media/users/' . $userId . '/post-' . $post->id . '-thumbnail-' . Str::random(10) . '.' . $thumb->getClientOriginalExtension();

                // Upload the file to S3
                Storage::disk(env('STORAGE_DISK', 's3'))->put($thumb_slug, file_get_contents($thumb));
                Storage::disk(env('STORAGE_DISK', 's3'))->put($content_slug, file_get_contents($content));

                $data = [
                    "user_id" => $userId,
                    "type" => $request['type'],
                    "size" => $request['size'],
                    "mediable_id" => $post->id,
                    "mediable_type" => $post->getMorphClass(),
                    "media_key" => $content_slug,
                    "thumbnail_key" => $thumb_slug,
                ];

                Media::create($data);

                return $this->jsonSuccess(200, 'Media Uploaded Successfully!');
            } else {
                return $this->jsonError(403, 'Post not found or deleted');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function deleteMedia($userId, $request): JsonResponse
    {
        try{
            Media::where('user_id', $userId)
                ->whereIn('id', $request['media_ids'])
                ->update(['status' => 'deleted']);

            return $this->jsonSuccess(204, 'Media Deleted Successfully!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
