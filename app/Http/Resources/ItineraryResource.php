<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryResource extends JsonResource
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
            'name' => $this->name,
            'user' => new UserShortResource($this->whenLoaded('user')),
            'posts_count' => $this->whenHas('posts_count'),
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
