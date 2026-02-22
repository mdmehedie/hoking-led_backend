<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'detailed_description' => $this->detailed_description,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'author_id' => $this->author_id,
            'is_featured' => $this->is_featured,
            'image_path' => $this->image_path ? url(Storage::url($this->image_path)) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
