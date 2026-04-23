<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Str;
use App\Traits\HasMedia;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;

class CaseStudyCategory extends Model
{
    use NodeTrait, HasMedia, HasSeo, HasTranslations;

    protected array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'icon',
        'is_visible',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sort_order',
        'parent_id',
    ];

    protected array $mediaAttributes = [
        'thumbnail',
        'icon',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CaseStudyCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CaseStudyCategory::class, 'parent_id');
    }

    public function caseStudies(): HasMany
    {
        return $this->hasMany(CaseStudy::class, 'category_id');
    }
}
