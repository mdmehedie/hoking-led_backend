<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CaseStudyResource extends JsonResource
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
            'image' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'project_description' => $this->transformProjectDescription($this->project_description),
            'project_details' => $this->project_details ?? [],
            'slider_images' => $this->transformSliderImages($this->slider_images),
            'translations' => $this->transformTranslations(),
            'url' => $this->getUrl(),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Transform project description and convert image paths to URLs.
     */
    private function transformProjectDescription($value): array
    {
        if (empty($value)) {
            return [];
        }

        // Value is already decoded by HasTranslations trait
        if (!is_array($value)) {
            return [];
        }

        // Convert image paths to URLs for each locale
        $transformed = [];
        foreach ($value as $locale => $items) {
            if (is_array($items)) {
                $transformed[$locale] = array_map(function ($item) {
                    if (isset($item['image'])) {
                        $item['image'] = Storage::disk('public')->url($item['image']);
                    }
                    return $item;
                }, $items);
            }
        }

        return $transformed;
    }

    /**
     * Transform slider images to full URLs.
     */
    private function transformSliderImages($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            return [];
        }

        return array_map(function ($image) {
            return Storage::disk('public')->url($image);
        }, $value);
    }

    /**
     * Transform translations.
     */
    private function transformTranslations(): array
    {
        if (!$this->translations) {
            return [];
        }

        return $this->translations->map(function ($translation) {
            return [
                'id' => $translation->id,
                'locale' => $translation->locale,
                'attribute' => $translation->attribute,
                'value' => $translation->value,
                'created_at' => $translation->created_at,
                'updated_at' => $translation->updated_at,
            ];
        })->toArray();
    }
}
