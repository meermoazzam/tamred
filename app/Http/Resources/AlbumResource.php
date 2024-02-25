<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
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
            'status' => $this->status,
            'posts_count' => $this->whenHas('posts_count'),
            'media_count' => $this->whenHas('media_count'),
            'created_at' => $this->created_at,
        ];
    }
}
