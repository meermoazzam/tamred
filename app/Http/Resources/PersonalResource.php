<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalResource extends JsonResource
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
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
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
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'status' => $this->status,
        ];
    }
}
