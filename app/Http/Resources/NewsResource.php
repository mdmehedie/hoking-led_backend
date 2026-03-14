<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'content' => $this->content,
            'translations' => $this->translations,
            'url' => $this->getUrl(),
            'image_path' => $this->image_path ? url(Storage::url($this->image_path)) : null,
            'published_at' => $this->published_at,
            'author_id' => $this->author_id,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'alternates' => $this->getAlternates(),
        ];
    }
}
