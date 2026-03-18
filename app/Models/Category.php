<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Str;
use App\Traits\HasSeo;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use NodeTrait, HasSeo;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
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

        static::deleted(function ($category) {
            if ($category->thumbnail) {
                Storage::disk('public')->delete($category->thumbnail);
            }
        });

        static::deleting(function ($category) {
            // Delete thumbnails from all children when deleting with children
            if (method_exists($category, 'children')) {
                $category->children->each(function ($child) {
                    if ($child->thumbnail) {
                        Storage::disk('public')->delete($child->thumbnail);
                    }
                });
            }
        });
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getUrl(): string
    {
        return url('/categories/' . $this->slug);
    }
}
