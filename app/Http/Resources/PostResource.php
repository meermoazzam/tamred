<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'user' => new UserShortResource($this->whenLoaded('user')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'my_reactions' => ReactionResource::collection($this->whenLoaded('reactions')),
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'tags' => $this->tags,
            'tagged_users' => $this->tagged_users,
            'total_likes' => $this->total_likes,
            'total_comments' => $this->total_comments,
            'allow_comments' => $this->allow_comments,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
