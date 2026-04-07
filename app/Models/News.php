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
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected array $mediaAttributes = [
        'image_path',
    ];

    protected $casts = [
        'published_at' => 'datetime',
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
}
