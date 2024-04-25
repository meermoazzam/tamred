<?php

namespace App\Services;

use Exception;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommentService extends Service {

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

    public function create(int $userId, Request $data): JsonResponse
    {
        try{
            $post = Post::where('id', $data['post_id'])->where('allow_comments', true)->status('published')->first();
            $parentComment = Comment::where('post_id', $data['post_id'])
                ->where('id', $data['parent_id'])->status('published')->first();

            // if either parent_id = null or have the parent comment then good
            if( ($data->input('parent_id') == null || $parentComment) && $post ) {
                $comment = Comment::create([
                    'user_id' => $userId,
                    'post_id' => $data['post_id'],
                    'parent_id' => $data['parent_id'],
                    'description' => $data['description'],
                    'status' => 'published',
                ]);
                $updatePostCommentCount = Post::where('id', $data['post_id'])->increment('total_comments');

                // WRITE ACTIVITY
                $this->activityService->generateActivity($post->user_id, $userId, 'commented', $post->id);
                // WRITE ACTIVITY
                $pattern = '/@(\S+)/';
                preg_match_all($pattern, $data['description'], $matches);
                $taggedUsernames = $matches[1];
                if(count($taggedUsernames)) {
                    foreach($taggedUsernames as $tag) {
                        $taggedUser = User::where('username', $tag)->first();
                        if($taggedUser) {
                            $this->activityService->generateActivity($taggedUser->id, $userId, 'tagged_in_comment', $comment->id);
                        }
                    }
                }


                return $this->jsonSuccess(201, 'Comment created successfully!', ['comment' => new CommentResource($comment)]);
            } else {
                return $this->jsonError(403, 'Failed to create comment, either post doesn\'t allow or parent comment not found.');
            }

        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(): JsonResponse
    {
        try{
            $comments = Comment::query();
            $comments->whereHas('post', function (Builder $query) {
                $query->where($query->qualifyColumn('status'), 'published');
            })
            ->where('post_id', request()->post_id)
            ->where('parent_id', request()->parent_id)
            ->status('published')
            ->with('user', 'children', 'children.user')
            ->withCount('children')
            ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['comments' => CommentResource::collection($comments->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function update(int $userId, int $id, array $data): JsonResponse
    {
        try{
            $isUpdated = Comment::where('id', $id)->where('user_id', $userId)
                ->whereHas('post', function (Builder $query) {
                    $query->where($query->qualifyColumn('status'), 'published');
                })
                ->statusNot(['archived', 'deleted'])
                ->update(['description' => $data['description']]);
            if( $isUpdated ) {
                return $this->jsonSuccess(200, 'Updated Successfully', ['comment' => new CommentResource(Comment::find($id))]);
            } else {
                return $this->jsonError(403, 'No comment found to udpate');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $comment = Comment::where('id', $id)->where('user_id', $userId)->statusNot('deleted')->first();

            if($comment) {
                $isDeleted = $comment->update(['status' => 'deleted']);
                $childComments = Comment::query();
                $childComments->where('parent_id', $id)
                   ->statusNot('deleted');
                $childCommentsCount = $childComments->count();
                $childComments->update(['status' => 'deleted']);

                // update comments count in posts.
                $updatePostCommentCount = Post::where('id', $comment->post_id)->decrement('total_comments', $childCommentsCount + 1);
                return $this->jsonSuccess(204, 'Comment Deleted successfully');
            } else {
                return $this->jsonError(403, 'No comment found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
