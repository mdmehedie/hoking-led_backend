<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Traits\HasMedia;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;

class Page extends Model
{
    use HasMedia, HasSeo, HasTranslations;

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
        'region',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected array $mediaAttributes = [
        'image_path',
    ];

    /**
     * Get translatable media keys dynamically based on slug.
     */
    public function getTranslatableMediaKeys(): array
    {
        return match ($this->slug) {
            'company' => [
                'content.*.hero_bg',
                'content.*.hero_video',
                'content.*.banner',
                'content.*.our_factory.image_1',
                'content.*.our_factory.image_2',
                'content.*.our_factory.image_3',
                'content.*.bottom_image',
            ],
            'about-us' => [
                'content.*.image_1',
                'content.*.image_2',
                'content.*.mission_vision_image',
                'content.*.mission.icon',
                'content.*.vision.icon',
            ],
            'after-sale-service' => [
                'content.*.hero_bg',
                'content.*.services.*.icon',
            ],
            'contact' => [
                'content.*.background',
                'content.*.contacts.*.icon',
            ],
            default => [
                'content.*.image',
            ],
        };
    }

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            if (!$page->slug) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'page_regions');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
