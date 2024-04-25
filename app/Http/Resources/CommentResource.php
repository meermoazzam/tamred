<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'user' => new UserShortResource($this->whenLoaded('user')),
            'parent_id' => (int)$this->parent_id,
            'description' => $this->description,
            'status' => $this->status,
            'children_count' => (int)$this->children_count ?? 0,
            'children' => CommentResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
