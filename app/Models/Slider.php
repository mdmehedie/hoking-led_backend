<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasMedia, HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'primary_button_text',
        'primary_button_link',
        'background_image',
        'foreground_image',
        'label',
        'status',
        'sort_order',
    ];

    protected $translatable = [
        'title',
        'description',
        'label',
        'primary_button_text',
    ];

    protected array $mediaAttributes = [
        'background_image',
        'foreground_image',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('sort_order');
        });
    }
}
