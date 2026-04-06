<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasTranslations, HasMedia, HasSeo;

    protected $translatable = [
        'title',
        'secondary_title',
        'description',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected $fillable = [
        'title',
        'secondary_title',
        'slug',
        'description',
        'excerpt',
        'cover_image',
        'gallery',
        'is_featured',
        'is_popular',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected array $mediaAttributes = [
        'cover_image',
        'gallery',
    ];

    protected $casts = [
        'gallery' => 'array',
        'project_date' => 'date',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sort_order)) {
                $model->sort_order = static::max('sort_order') + 1;
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
