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
            'id' => (int)$this->id,
            'name' => $this->name,
            'album_id' => $this->album_id,
            'user' => new UserShortResource($this->whenLoaded('user')),
            'collaborators' => UserShortResource::collection($this->whenLoaded('collaborators')),
            'posts_count' => (int)$this->whenHas('posts_count', $this->posts_count, 0),
            'collaborators_count' => (int)$this->whenHas('collaborators_count', $this->collaborators_count, 0),
            'via_collab' => (bool)$this->whenHas('via_collab', $this->via_collab, false),
            'status' => $this->status,
            'is_collaborative' => $this->is_collaborative,
            'created_at' => $this->created_at,
        ];
    }
}
