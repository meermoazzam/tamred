<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Album\CreateRequest;
use App\Http\Requests\Album\UpdateRequest;
use App\Services\AlbumService;
use Illuminate\Http\JsonResponse;

class AlbumController extends ApiController
{
    /**
	* @var albumService
	*/
	private $albumService;

	/**
    * @param AlbumService
    */
    public function __construct(AlbumService $albumService) {
    	$this->albumService = $albumService;
    }

    public function create(CreateRequest $request): JsonResponse
    {
        return $this->albumService->create(auth()->id(), $request->validated());
    }

    public function get($id): JsonResponse
    {
        return $this->albumService->get(auth()->id(), $id);
    }

    public function list(): JsonResponse
    {
        return $this->albumService->list(auth()->id());
    }

    public function update(UpdateRequest $request, $id): JsonResponse
    {
        return $this->albumService->update(auth()->id(), $id, $request->validated());
    }

    public function delete($id): JsonResponse
    {
        return $this->albumService->delete(auth()->id(), $id);
    }
}
