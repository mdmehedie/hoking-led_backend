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
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'detailed_description' => $this->transformDetailedDescription($this->detailed_description),
            'features' => $this->transformFeatures($this->features),
            'translations' => $this->transformTranslations(),
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

    /**
     * Transform translations and decode JSON values.
     */
    private function transformTranslations(): array
    {
        if (!$this->translations) {
            return [];
        }

        return $this->translations->map(function ($translation) {
            $value = $translation->value;
            
            // Decode JSON for structured fields
            if (in_array($translation->attribute, ['detailed_description', 'features'])) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                    
                    // Convert image paths to URLs for detailed_description
                    if ($translation->attribute === 'detailed_description' && is_array($value)) {
                        foreach ($value as $locale => $items) {
                            if (is_array($items)) {
                                foreach ($items as $key => $item) {
                                    if (isset($item['image'])) {
                                        $value[$locale][$key]['image'] = Storage::disk('public')->url($item['image']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            return [
                'id' => $translation->id,
                'locale' => $translation->locale,
                'attribute' => $translation->attribute,
                'value' => $value,
                'created_at' => $translation->created_at,
                'updated_at' => $translation->updated_at,
            ];
        })->toArray();
    }

    /**
     * Transform features field.
     */
    private function transformFeatures($value): array
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

        // Return locale-based format as-is
        if (isset($value['en']) || isset($value['bd'])) {
            return $value;
        }

        return $value;
    }

    /**
     * Transform detailed_description and convert image paths to URLs.
     */
    private function transformDetailedDescription($value): array
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

        // Check if locale-based or flat array
        $isLocaleFormat = false;
        foreach ($value as $key => $val) {
            if (is_array($val) && isset($val[0]['image'])) {
                $isLocaleFormat = true;
                break;
            }
        }
        
        if ($isLocaleFormat) {
            // Locale-based format
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
        
        // Flat array format
        return array_map(function ($item) {
            if (isset($item['image'])) {
                $item['image'] = Storage::disk('public')->url($item['image']);
            }
            return $item;
        }, $value);
    }
}
