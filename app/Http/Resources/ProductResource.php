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
            'translations' => $this->translations,
            'url' => $this->getUrl(),
            'category_id' => $this->category_id,
            'category' => $this->when($this->category, function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'status' => $this->status,
            'published_at' => $this->published_at,
            'author_id' => $this->author_id,
            'is_featured' => $this->is_featured,
            'main_image' => $this->main_image ? Storage::disk('public')->url($this->main_image) : null,
            'gallery' => $this->gallery ? array_map(function ($image) {
                return Storage::disk('public')->url($image);
            }, $this->gallery) : [],
            'video_embeds' => $this->video_embeds ?? [],
            'downloads' => $this->downloads ? array_map(function ($download) {
                return Storage::disk('public')->url($download);
            }, $this->downloads) : [],
            'technical_specs' => $this->technical_specs ?? [],
            'tags' => $this->tags ?? [],
            'related_products' => $this->when($this->relatedProducts, function () {
                return $this->relatedProducts->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'main_image' => $product->main_image ? Storage::disk('public')->url($product->main_image) : null,
                        'short_description' => $product->short_description,
                        'url' => $product->getUrl(),
                    ];
                });
            }),
            'regions' => $this->when($this->regions, function () {
                return $this->regions->map(function ($region) {
                    return [
                        'id' => $region->id,
                        'name' => $region->name,
                        'code' => $region->code,
                        'is_active' => $region->is_active,
                        'is_default' => $region->is_default,
                    ];
                });
            }),
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
