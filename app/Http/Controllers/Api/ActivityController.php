<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
	* @var activityService
	*/
	private $activityService;

	/**
    * @param ActivityService
    */
    public function __construct(ActivityService $activityService) {
    	$this->activityService = $activityService;
    }

    public function list(): JsonResponse
    {
        return $this->activityService->list(auth()->id());
    }

    public function activitiesBy24hours(): JsonResponse
    {
        return $this->activityService->activitiesBy24hours(auth()->id());
    }

    public function markAsRead(): JsonResponse
    {
        return $this->activityService->markAsRead(auth()->id());
    }
}
