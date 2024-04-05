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
            'id' => (int)$this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'bio' => $this->bio,
            'username' => $this->username,
            'nickname' => $this->nickname,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'location' => (string)$this->location,
            'latitude' => (string)$this->latitude,
            'longitude' => (string)$this->longitude,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'language' => $this->language,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'post_count' => (int)$this->post_count ?? 0,
            'country_count' => (int)$this->country_count ?? 0,
            'album_count' => (int)$this->whenHas('album_count', $this->album_count, 0),
            'follower_count' => (int)$this->follower_count ?? 0,
            'following_count' => (int)$this->following_count ?? 0,
            'in_my_following' => (bool)$this->whenHas('inMyFollowing'),
            'is_my_follower' => (bool)$this->whenHas('isMyFollower'),
            'created_at' => $this->created_at,
            'status' => $this->status,
        ];
    }
}
