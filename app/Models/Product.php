<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'title',
        'short_description',
        'detailed_description',
        'status',
        'published_at',
        'technical_specs',
        'tags',
        'video_embeds',
        'category_id',
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'tags' => 'array',
        'video_embeds' => 'array',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')->acceptsMimeTypes(['image/*']);
        $this->addMediaCollection('videos')->acceptsMimeTypes(['video/*']);
        $this->addMediaCollection('downloads')->acceptsMimeTypes([
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
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

    public function getDetailedDescriptionAttribute($value)
    {
        if (is_string($value) && $decoded = json_decode($value, true)) {
            if (is_array($decoded) && isset($decoded['en'])) {
                return $decoded['en'];
            }
        }
        return $value;
    }
}
