<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AttachCategoryRequest;
use App\Http\Requests\Post\BindAlbumRequest;
use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\DeleteMediaRequest;
use App\Http\Requests\Post\ListRequest;
use App\Http\Requests\Post\ReactRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Requests\Post\UploadMediaRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
	* @var postService
	*/
	private $postService;

	/**
    * @param PostService
    */
    public function __construct(PostService $postService) {
    	$this->postService = $postService;
    }

    public function create(CreateRequest $request): JsonResponse
    {
        return $this->postService->create(auth()->id(), $request);
    }

    public function publish($id): JsonResponse
    {
        return $this->postService->publish(auth()->id(), $id);
    }

    public function get($id): JsonResponse
    {
        return $this->postService->get(auth()->id(), $id);
    }

    public function getByCommentId($id): JsonResponse
    {
        return $this->postService->getByCommentId(auth()->id(), $id);
    }

    public function list(ListRequest $request): JsonResponse
    {
        return $this->postService->list(auth()->id());
    }

    public function listByAlbumId(ListRequest $request): JsonResponse
    {
        return $this->postService->listByAlbumId(auth()->id());
    }

    public function listForHome(): JsonResponse
    {
        return $this->postService->listForHome(auth()->id());
    }

    public function listByMostFollowedPeople(): JsonResponse
    {
        return $this->postService->listByMostFollowedPeople(auth()->id());
    }

    public function listByNearMe(): JsonResponse
    {
        return $this->postService->listByNearMe(auth()->id());
    }

    public function listByUsersIFollow(): JsonResponse
    {
        return $this->postService->listByUsersIFollow(auth()->id());
    }

    public function listByMyFriends(): JsonResponse
    {
        return $this->postService->listByMyFriends(auth()->id());
    }

    public function listByRandom(): JsonResponse
    {
        return $this->postService->listByRandom(auth()->id());
    }

    public function update(UpdateRequest $request, $id): JsonResponse
    {
        return $this->postService->update(auth()->id(), $id, $request->validated());
    }

    public function delete($id): JsonResponse
    {
        return $this->postService->delete(auth()->id(), $id);
    }

    public function attachCategories(AttachCategoryRequest $request, $id): JsonResponse
    {
        return $this->postService->attachCategories(auth()->id(), $id);
    }

    public function react(ReactRequest $request, $id): JsonResponse
    {
        return $this->postService->react(auth()->id(), $id);
    }

    public function reactList($id): JsonResponse
    {
        return $this->postService->reactList($id);
    }

    public function uploadMedia(UploadMediaRequest $request): JsonResponse
    {
        return $this->postService->uploadMedia(auth()->id(), $request->validated());
    }

    public function deleteMedia(DeleteMediaRequest $request): JsonResponse
    {
        return $this->postService->deleteMedia(auth()->id(), $request->validated());
    }

}
