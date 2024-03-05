<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'sender' => new UserShortResource($this->whenLoaded('sender')),
            'model_id' => (int)$this->model_id,
            'type' => $this->type,
            'is_read' => (bool)$this->is_read,
            'created_at' => $this->created_at,
        ];
    }
}
