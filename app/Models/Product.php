<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;
use Illuminate\Support\Facades\Storage;

class Product extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, HasSeo;

    protected $translatable = [
        'title',
        'short_description',
        'detailed_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'features',
    ];

    protected $fillable = [
        'title',
        'short_description',
        'detailed_description',
        'status',
        'published_at',
        'technical_specs',
        'tags',
        'video_embeds',
        'main_image',
        'gallery',
        'downloads',
        'category_id',
        'slug',
        'is_featured',
        'order_column',
        'features',
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'video_embeds' => 'array',
        'gallery' => 'array',
        'downloads' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Transform tags between database format [{"tag":"value"},...] 
     * and Filament format ["value",...]
     */
    protected function tags(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return [];
                }
                
                $tags = is_string($value) ? json_decode($value, true) : $value;
                
                if (is_array($tags) && isset($tags[0]['tag'])) {
                    // Transform from [{"tag":"value"},...] to ["value",...]
                    return array_column($tags, 'tag');
                }
                
                return $tags ?? [];
            },
            set: function ($value) {
                // Ensure tags is an array
                $tags = is_array($value) ? $value : [];
                
                // Transform from ["value",...] to [{"tag":"value"},...] and JSON encode
                return json_encode(array_map(function ($tag) {
                    return ['tag' => $tag];
                }, $tags));
            }
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($product) {
            // Delete old main image if being replaced
            if ($product->isDirty('main_image') && $product->getOriginal('main_image')) {
                Storage::disk('public')->delete($product->getOriginal('main_image'));
            }

            // Delete removed gallery images
            if ($product->isDirty('gallery')) {
                $oldGallery = $product->getOriginal('gallery') ?? [];
                $newGallery = $product->gallery ?? [];
                $toDelete = array_diff($oldGallery, $newGallery);
                foreach ($toDelete as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Delete removed downloads
            if ($product->isDirty('downloads')) {
                $oldDownloads = $product->getOriginal('downloads') ?? [];
                $newDownloads = $product->downloads ?? [];
                $toDelete = array_diff($oldDownloads, $newDownloads);
                foreach ($toDelete as $download) {
                    Storage::disk('public')->delete($download);
                }
            }

            // Delete removed description images
            if ($product->isDirty('detailed_description')) {
                $oldImages = static::collectDescriptionImages($product->getOriginal('detailed_description'));
                $newImages = static::collectDescriptionImages($product->detailed_description);
                $toDelete = array_diff($oldImages, $newImages);
                foreach ($toDelete as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
        });

        static::deleting(function ($product) {
            // Delete main image
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }

            // Delete gallery images
            if ($product->gallery) {
                foreach ($product->gallery as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Delete downloads
            if ($product->downloads) {
                foreach ($product->downloads as $download) {
                    Storage::disk('public')->delete($download);
                }
            }

            // Delete video files
            if ($product->video_embeds) {
                foreach ($product->video_embeds as $embed) {
                    if (isset($embed['type']) && $embed['type'] === 'file' && isset($embed['video_file'])) {
                        Storage::disk('public')->delete($embed['video_file']);
                    }
                }
            }

            // Delete description images
            foreach (static::collectDescriptionImages($product->detailed_description) as $image) {
                Storage::disk('public')->delete($image);
            }
        });
    }

    /**
     * Collect all image paths from description data.
     */
    private static function collectDescriptionImages($descriptions): array
    {
        $images = [];
        
        if (is_string($descriptions)) {
            $descriptions = json_decode($descriptions, true);
        }
        
        if (!is_array($descriptions)) {
            return $images;
        }

        foreach ($descriptions as $locale => $items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['image'])) {
                        $images[] = $item['image'];
                    }
                }
            }
        }

        return $images;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_product_id');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'product_regions');
    }

    public function registerMediaCollections(): void
    {
        // No media collections needed, using file uploads to fields
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);
        $this->addMediaConversion('medium')
            ->width(600)
            ->height(600)
            ->sharpen(10);
        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1200)
            ->sharpen(10);
    }

    public function getUrl(): string
    {
        return config('app.url') . '/api/v1/products/' . $this->slug;
    }

    public function getAlternates(): array
    {
        $alternates = [];

        // Get regions where this product is available
        $productRegions = $this->regions()->where('is_active', true)->pluck('code')->toArray();

        // If no regions specified, use default region only
        if (empty($productRegions)) {
            $productRegions = [\App\Models\Region::defaultCode()];
        }

        // For proper hreflang, generate alternates for each region with its default locale
        foreach ($productRegions as $region) {
            $url = $this->getUrl();

            // Map regions to their typical locales
            $regionToLocale = [
                'us' => 'en',
                'uk' => 'en-GB',
                'eu' => 'en',
                'ca' => 'en-CA',
                'au' => 'en-AU',
                'bd' => 'bd'  // Use 'bd' locale code since that's what's in the database
            ];

            $locale = $regionToLocale[$region] ?? 'en';

            // For default region (us), don't add prefix
            if ($region === \App\Models\Region::defaultCode()) {
                $alternates[] = [
                    'locale' => $locale,
                    'url' => $url
                ];
            } else {
                // Add region prefix for non-default regions
                $alternates[] = [
                    'locale' => $locale,
                    'url' => str_replace(url('/api/v1/products'), url('/api/v1/' . $region . '/products'), $url)
                ];
            }
        }

        return $alternates;
    }
}
