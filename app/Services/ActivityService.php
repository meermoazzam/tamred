<?php

namespace App\Services;

use App\Http\Resources\ActivityResource;
use App\Models\Activities;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

class ActivityService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * ActivityService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function list($userId): JsonResponse
    {
        try{
            $activities = Activities::query();
            $activities->where('user_id', $userId)
                ->with('sender')
                ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['activities' => ActivityResource::collection($activities->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function markAsRead(int $userId): JsonResponse
    {
        try{
            $activity_ids = request()->activity_id;
            $activity_ids = is_array($activity_ids) ? $activity_ids : [$activity_ids];

            $isRead = Activities::when(request()->activity_id, function(Builder $query) use ($activity_ids) {
                $query->whereIn('id', $activity_ids);
            })
            ->where('user_id', $userId)
            ->update([
                'is_read' => true,
            ]);

            return $this->jsonSuccess(200, 'Successfully marked as read!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function generateActivity(int $forUserId, int $causedByUserId, string $type, int $modelId = null, string $message = null) {
        try {
            Activities::create([
                'user_id' => $forUserId,
                'caused_by' => $causedByUserId,
                'model_id' => $modelId,
                'type' => $type,
                'message' => $message,
            ]);
        } catch (Exception $e) {
            // WIP
        }
    }
}
