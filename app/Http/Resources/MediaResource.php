<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'type' => $this->type,
            'size' => (int)$this->size,
            'media_url' => $this->media_key,
            'thumbnail_url' => $this->thumbnail_key,
            'created_at' => $this->created_at,
        ];
    }
}
