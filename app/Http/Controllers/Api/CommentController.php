<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CreateRequest;
use App\Http\Requests\Comment\ListRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
	* @var commentService
	*/
	private $commentService;

	/**
    * @param CommentService
    */
    public function __construct(CommentService $commentService) {
    	$this->commentService = $commentService;
    }

    public function create(CreateRequest $request): JsonResponse
    {
        return $this->commentService->create(auth()->id(), $request);
    }

    public function list(ListRequest $request): JsonResponse
    {
        return $this->commentService->list();
    }

    public function update(UpdateRequest $request, $id): JsonResponse
    {
        return $this->commentService->update(auth()->id(), $id, $request->validated());
    }

    public function delete($id): JsonResponse
    {
        return $this->commentService->delete(auth()->id(), $id);
    }
}
