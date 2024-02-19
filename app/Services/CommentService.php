<?php

namespace App\Services;

use Exception;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommentService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * CommentService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function create(int $userId, Request $data): JsonResponse
    {
        try{
            $post = Post::where('id', $data['post_id'])->status('published')->first();
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
                return $this->jsonSuccess(201, 'Comment created successfully!', ['comment' => new CommentResource($comment)]);
            } else {
                return $this->jsonError(403, 'Failed to create comment, either post or parent comment not found.');
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
            ->with('user')
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
            // fetch post
            $post_id = Comment::where('id', $id)->pluck('post_id')->first();

            // delete comment and its child comments
            $comments = Comment::query();
            $comments->where(function (Builder $query) use ($userId, $id) {
                $query->where('id', $id)->where('user_id', $userId);
            })->orWhere('parent_id', $id)
            ->statusNot('deleted');

            // get total commetents to be deleted & delete comments
            $selectedCommentsCount = $comments->count();
            $isDeleted = $comments->update(['status' => 'deleted']);

            if( $isDeleted ) {
                // update comments count in posts.
                $updatePostCommentCount = Post::where('id', $post_id)->decrement('total_comments', $selectedCommentsCount);
                return $this->jsonSuccess(204, 'Comment Deleted successfully');
            } else {
                return $this->jsonError(403, 'No comment found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
