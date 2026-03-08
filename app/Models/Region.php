<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Region extends Model
{
    protected $fillable = [
        'code',
        'name',
        'currency',
        'timezone',
        'language',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $region): void {
            if ($region->is_default) {
                DB::table('regions')
                    ->where('id', '!=', $region->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public static function defaultCode(): string
    {
        return static::query()->where('is_default', true)->value('code')
            ?? config('app.default_region', 'us');
    }

    public static function activeCodes(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->pluck('code')
            ->values()
            ->all();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_regions');
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_regions');
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'page_regions');
    }

    public function caseStudies(): BelongsToMany
    {
        return $this->belongsToMany(CaseStudy::class, 'case_study_regions');
    }

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_regions');
    }
}
