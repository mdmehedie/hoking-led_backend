<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Traits\HasMedia;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;

class CaseStudy extends Model
{
    use HasMedia, HasSeo, HasTranslations;

    protected array $translatable = [
        'title',
        'excerpt',
        'project_description',
        'project_details',
        'bullet_fields',
    ];

    protected $fillable = [
        'category_id',
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
        'project_description',
        'project_details',
        'bullet_fields',
    ];

    protected array $mediaAttributes = [
        'image_path',
        'slider_images',
    ];

    protected array $translatableMediaKeys = [
        'project_description.*.image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'slider_images' => 'array',
        'bullet_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($caseStudy) {
            if (!$caseStudy->slug) {
                $caseStudy->slug = Str::slug($caseStudy->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CaseStudyCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'case_study_regions');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
