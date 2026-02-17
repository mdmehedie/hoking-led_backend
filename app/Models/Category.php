<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Str;

class Category extends Model
{
    use NodeTrait;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_visible',
        'meta_title',
        'meta_description',
        'meta_keywords',
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
}
