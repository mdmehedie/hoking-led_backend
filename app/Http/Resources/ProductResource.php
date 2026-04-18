<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'detailed_description' => $this->transformDetailedDescription(),
            'features' => $this->transformFeatures(),
            'technical_specs' => $this->technical_specs ?? [],
            'tags' => $this->tags ?? [],
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'is_top' => (bool) $this->is_top,
            'published_at' => $this->published_at,
            'category' => $this->when($this->category, fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'main_image' => $this->main_image ? Storage::disk('public')->url($this->main_image) : null,
            'gallery' => $this->gallery ? array_map(
                fn ($img) => Storage::disk('public')->url($img),
                $this->gallery
            ) : [],
            'video_embeds' => $this->transformVideoEmbeds(),
            'downloads' => $this->downloads ? array_map(
                fn ($dl) => Storage::disk('public')->url($dl),
                $this->downloads
            ) : [],
            'related_products' => $this->when($this->relatedProducts, fn () =>
                $this->relatedProducts
                    ->filter(fn ($p) => $p->id !== $this->id)
                    ->map(fn ($p) => [
                        'id' => $p->id,
                        'title' => $p->title,
                        'slug' => $p->slug,
                        'main_image' => $p->main_image ? Storage::disk('public')->url($p->main_image) : null,
                        'short_description' => $p->short_description,
                    ])
            ),
            'sort_order' => $this->order_column, 
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function transformDetailedDescription(): array
    {
        $value = $this->detailed_description;

        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            return [];
        }

        return $this->convertMediaPathsToUrls($value);
    }

    private function transformFeatures(): array
    {
        $value = $this->features;

        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            return [];
        }

        // Return locale-based format as-is
        if (isset($value['en']) || isset($value['bd'])) {
            return $value;
        }

        return $value;
    }

    private function transformVideoEmbeds(): array
    {
        $value = $this->video_embeds;

        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            return [];
        }

        return $this->convertMediaPathsToUrls($value);
    }

    /**
     * Recursively convert file paths to URLs.
     * Skips full URLs (youtube, vimeo, etc.) and text values.
     */
    private function convertMediaPathsToUrls(array $data): array
    {
        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $data[$key] = $this->convertMediaPathsToUrls($item);
            } elseif (is_string($item) && str_contains($item, 'products/')) {
                $data[$key] = Storage::disk('public')->url($item);
            }
        }
        return $data;
    }
}
