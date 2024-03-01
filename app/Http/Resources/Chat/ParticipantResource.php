<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\UserShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
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
            'user_id' => $this->user_id,
            'conversation_id' => $this->conversation_id,
            'user' => new UserShortResource($this->whenLoaded('user')),
            'status' => $this->status,
            'message_status' => $this->message_status,
            'seen_at' => $this->seen_at,
            'created_at' => $this->created_at,
        ];
    }
}
