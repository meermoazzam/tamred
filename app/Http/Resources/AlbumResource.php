<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'collaborators' => UserShortResource::collection($this->whenLoaded('collaborators')),
            'posts_count' => (int)$this->whenHas('posts_count', $this->posts_count, 0),
            'post_ids' => $this->whenHas('post_ids', $this->post_ids, []),
            'itineraries_count' => (int)$this->whenHas('itineraries_count', $this->itineraries_count, 0),
            'collaborators_count' => (int)$this->whenHas('collaborators_count', $this->collaborators_count, 0),
            'media_count' => (int)$this->whenHas('media_count', $this->media_count, 0),
            'itineraries' => ItineraryResource::collection($this->whenLoaded('itineraries')),
            'first_media' => new MediaResource($this->whenHas('first_media')),
            'first_post' => new PostResource($this->whenHas('first_post')),
            'status' => $this->status,
            'via_collab' => (bool)$this->whenHas('via_collab', $this->via_collab, false),
            'is_collaborative' => $this->is_collaborative,
            'default_image' => Storage::disk('public')->url("/images/album.png"),
            'created_at' => $this->created_at,
        ];
    }
}
