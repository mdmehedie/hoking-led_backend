<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasMedia, HasSeo, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'position',
        'bio',
        'email',
        'phone',
        'photo',
        'social_links',
        'sort_order',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected $translatable = [
        'name',
        'position',
        'bio',
    ];

    protected array $mediaAttributes = [
        'photo',
    ];

    protected $casts = [
        'social_links' => 'array',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
