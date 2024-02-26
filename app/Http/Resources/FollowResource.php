<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowResource extends JsonResource
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
            'follower' => new UserShortResource($this->whenLoaded('userDetailByUserId')),
            'following' => new UserShortResource($this->whenLoaded('userDetailByFollowedId')),
            'in_my_following' => $this->whenHas('inMyFollowing'),
            'is_my_follower' => $this->whenHas('isMyFollower'),
            'created_at' => $this->created_at,
        ];
    }
}
