<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'link',
        'alt_text',
        'order',
        'status',
        'custom_styles',
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
        'custom_styles' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('order');
        });
    }
}
