<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'bio' => $this->bio,
            'username' => $this->username,
            'nickname' => $this->nickname,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'post_count' => $this->post_count ?? 0,
            'album_count' => $this->whenHas('album_count'),
            'follower_count' => $this->follower_count ?? 0,
            'following_count' => $this->following_count ?? 0,
            'in_my_following' => $this->whenHas('inMyFollowing'),
            'is_my_follower' => $this->whenHas('isMyFollower'),
            'created_at' => $this->created_at,
            'status' => $this->status,
        ];
    }
}
