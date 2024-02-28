<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->when($this->status, $this->status),
            'description' => $this->when(($this->status != 'deleted'), $this->description),
            'media' => new MediaResource($this->whenLoaded('media')),
            'created_at' => $this->created_at,
        ];
    }
}
