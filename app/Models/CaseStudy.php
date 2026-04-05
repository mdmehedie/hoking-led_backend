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
use Illuminate\Support\Facades\Storage;

class CaseStudy extends Model implements HasMedia
{
    use InteractsWithMedia, HasSeo, HasTranslations;

    protected array $translatable = [
        'title',
        'excerpt',
        'image_path',
        'project_description',
        'project_details',
    ];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'image_path',
        'author_id',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'slider_images',
        'canonical_url',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'slider_images' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($caseStudy) {
            // Delete image files from translatable fields (for all locales)
            foreach ($caseStudy->translatable as $attribute) {
                if ($attribute === 'image_path') {
                    foreach (['en', 'bd'] as $locale) {
                        $filePath = $caseStudy->getTranslation($attribute, $locale, false);
                        if ($filePath) {
                            Storage::disk('public')->delete($filePath);
                        }
                    }
                }
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'case_study_regions');
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
        return url('/api/v1/case-studies/' . $this->slug);
    }

    public function getAlternates(): array
    {
        $alternates = [];

        // Get regions where this case study is available
        $caseStudyRegions = $this->regions()->where('is_active', true)->pluck('code')->toArray();

        // If no regions specified, use default region only
        if (empty($caseStudyRegions)) {
            $caseStudyRegions = [\App\Models\Region::defaultCode()];
        }

        // For proper hreflang, generate alternates for each region with its default locale
        foreach ($caseStudyRegions as $region) {
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
                    'url' => str_replace(url('/api/v1/case-studies'), url('/api/v1/' . $region . '/case-studies'), $url)
                ];
            }
        }

        return $alternates;
    }
}
