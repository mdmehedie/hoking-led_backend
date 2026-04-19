<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Traits\HasMedia;
use App\Traits\HasSeo;

class Video extends Model
{
    use HasMedia, HasSeo;

    protected $fillable = [
        'slug',
        'video_url',
        'video_path',
        'thumbnail_path',
    ];

    protected array $mediaAttributes = [
        'thumbnail_path',
        'video_path',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($video) {
            if (!$video->slug) {
                // This shouldn't happen with fixed slugs, but kept for safety
                $video->slug = Str::slug($video->id);
            }
        });
    }
}
