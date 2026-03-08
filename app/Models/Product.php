<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;

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
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'tags' => 'array',
        'video_embeds' => 'array',
        'gallery' => 'array',
        'downloads' => 'array',
        'published_at' => 'datetime',
    ];

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
        return url('/api/v1/products/' . $this->slug);
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
