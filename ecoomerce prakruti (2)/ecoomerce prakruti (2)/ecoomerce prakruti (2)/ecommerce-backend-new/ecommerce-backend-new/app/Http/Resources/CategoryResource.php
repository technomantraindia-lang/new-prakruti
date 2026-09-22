<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'gst_percentage' => (float) ($this->gst_percentage ?? 0),
            'image' => $this->image_url,
            'parent_id' => $this->parent_id,
            'count' => (int) ($this->products_count ?? 0),
        ];
    }
}
