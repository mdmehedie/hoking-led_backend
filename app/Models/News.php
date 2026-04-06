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

class News extends Model implements HasMedia
{
    use InteractsWithMedia, HasSeo, HasTranslations;

    protected array $translatable = [
        'title',
        'excerpt',
        'content',
        'image_path',
    ];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image_path',
        'author_id',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'news_regions');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
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
    }

    public function getUrl(): string
    {
        return route('news.show', ['slug' => $this->slug]);
    }

    public function getAlternates(): array
    {
        $alternates = [];
        
        // Get regions where this news article is available
        $newsRegions = $this->regions()->where('is_active', true)->pluck('code')->toArray();
        
        // If no regions specified, use default region only
        if (empty($newsRegions)) {
            $newsRegions = [\App\Models\Region::defaultCode()];
        }
        
        // For proper hreflang, generate alternates for each region with its default locale
        foreach ($newsRegions as $region) {
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
                    'url' => str_replace(url('/api/v1/news'), url('/api/v1/' . $region . '/news'), $url)
                ];
            }
        }
        
        return $alternates;
    }
}
