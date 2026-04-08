<?php

namespace App\Http\Resources;

use App\Helpers\ContentUrlHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => ContentUrlHelper::convertImageUrlsToAbsolute($this->content),
            'image' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'is_popular' => (bool) $this->is_popular,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'author' => $this->when($this->author, fn () => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ]),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
