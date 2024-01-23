<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreateRequest;
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
        return $this->postService->get(auth()->id(), $id);
    }

    public function list(): JsonResponse
    {
        return $this->postService->list(auth()->id());
    }

    public function update(UpdateRequest $request, $id): JsonResponse
    {
        return $this->postService->update(auth()->id(), $id, $request->validated());
    }

    public function delete($id): JsonResponse
    {
        return $this->postService->delete(auth()->id(), $id);
    }
}
