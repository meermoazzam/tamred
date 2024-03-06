<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
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
            'participants' => ParticipantResource::collection($this->whenLoaded('participants')),
            'latest_message' => new MessageResource($this->whenLoaded('latestMessage')),
            'unseen_message_count' => (int)$this->unseen_message_count ?? 0,
            'created_at' => $this->created_at,
        ];
    }
}
