<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'secondary_title' => $this->secondary_title,
            'slug' => $this->slug,
            'description' => $this->description,
            'excerpt' => $this->excerpt,
            'cover_image' => $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null,
            'gallery' => $this->gallery ? array_map(
                fn ($img) => Storage::disk('public')->url($img),
                $this->gallery
            ) : [],
            'is_featured' => (bool) $this->is_featured,
            'is_popular' => (bool) $this->is_popular,
            'is_successful' => (bool) $this->is_successful,
            'sort_order' => $this->sort_order,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
