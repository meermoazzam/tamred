<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Itin\CreateRequest;
use App\Http\Requests\Itin\UpdateRequest;
use App\Services\ItinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItinController extends ApiController
{
    /**
	* @var itinService
	*/
	private $itinService;

	/**
    * @param ItinService
    */
    public function __construct(ItinService $itinService) {
    	$this->itinService = $itinService;
    }

    public function create(CreateRequest $request): JsonResponse
    {
        return $this->itinService->create(auth()->id(), $request);
    }

    public function get($id): JsonResponse
    {
        return $this->itinService->get(auth()->id(), $id);
    }

    public function list(): JsonResponse
    {
        return $this->itinService->list(auth()->id());
    }

    public function update(UpdateRequest $request, $id): JsonResponse
    {
        return $this->itinService->update(auth()->id(), $id, $request);
    }

    public function delete($id): JsonResponse
    {
        return $this->itinService->delete(auth()->id(), $id);
    }

    public function deleteMultiple(Request $request): JsonResponse
    {
        return $this->itinService->deleteMultiple(auth()->id(), $request);
    }
}
