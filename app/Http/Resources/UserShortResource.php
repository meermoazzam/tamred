<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserShortResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'username' => $this->username,
            'location' => (string)$this->location,
            'latitude' => (string)$this->latitude,
            'longitude' => (string)$this->longitude,
            'email' => $this->email,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'in_my_following' => $this->whenHas('inMyFollowing'),
            'is_my_follower' => $this->whenHas('isMyFollower'),
        ];
    }
}
