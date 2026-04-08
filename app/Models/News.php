<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Traits\HasMedia;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;

class News extends Model
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
        'is_popular',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected array $mediaAttributes = [
        'image_path',
    ];

    protected array $translatableMediaKeys = [
        'content.*.image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_popular' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($news) {
            if (!$news->slug) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'news_regions');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeRecent($query, int $count = 5)
    {
        return $query->orderBy('published_at', 'desc')->limit($count);
    }
}
