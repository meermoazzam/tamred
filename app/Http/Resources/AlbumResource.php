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
            'id' => (int)$this->id,
            'name' => $this->name,
            'user' => new UserShortResource($this->whenLoaded('user')),
            'posts_count' => (int)$this->whenHas('posts_count', $this->posts_count, 0),
            'media_count' => (int)$this->whenHas('media_count', $this->media_count, 0),
            'itinerary' => new ItineraryResource($this->whenLoaded('itinerary')),
            'first_media' => new MediaResource($this->whenHas('first_media')),
            'first_post' => new PostResource($this->whenHas('first_post')),
            'status' => $this->status,
            'dafault_image' => config("app.url") . "/images/temp.jpg",
            'created_at' => $this->created_at,
        ];
    }
}
