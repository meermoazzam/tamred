<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AttachCategory;
use App\Http\Requests\Post\BindAlbum;
use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\ListRequest;
use App\Http\Requests\Post\UpdateRequest;
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
        return $this->postService->get($id);
    }

    public function list(ListRequest $request): JsonResponse
    {
        return $this->postService->list();
    }

    public function update(UpdateRequest $request, $id): JsonResponse
    {
        return $this->postService->update(auth()->id(), $id, $request->validated());
    }

    public function delete($id): JsonResponse
    {
        return $this->postService->delete(auth()->id(), $id);
    }

    public function attachCategory(AttachCategory $request, $id): JsonResponse
    {
        return $this->postService->attachCategory(auth()->id(), $id);
    }

    public function bindAlbum(BindAlbum $request, $id): JsonResponse
    {
        return $this->postService->bindAlbum(auth()->id(), $id);
    }

}
