<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'italian_name' => $this->italian_name,
            'parent_id' => (int)$this->parent_id,
            'icon' => $this->icon,
            'sub_categories_count' => (int)$this->whenHas('subCategories_count', $this->subCategories_count, 0),
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'sub_categories' => CategoryResource::collection($this->whenLoaded('subCategories')),
            'created_at' => $this->created_at,
        ];
    }
}
