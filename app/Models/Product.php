<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasMedia, HasSeo;

    protected $fillable = [
        'title',
        'short_description',
        'detailed_description',
        'status',
        'published_at',
        'technical_specs',
        'tags',
        'video_embeds',
        'main_image',
        'gallery',
        'downloads',
        'category_id',
        'slug',
        'is_featured',
        'is_top',
        'order_column',
        'features',
    ];

    protected array $mediaAttributes = [
        'main_image',
        'gallery',
        'downloads',
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'video_embeds' => 'array',
        'detailed_description' => 'array',
        'features' => 'array',
        'gallery' => 'array',
        'downloads' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_top' => 'boolean',
    ];

    public function getDetailedDescriptionAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        $data = is_array($value) ? $value : json_decode($value, true);

        if (!is_array($data)) {
            return [];
        }

        // Normalize format for each item in the repeater
        return array_map(function ($item) {
            if (is_array($item)) {
                if (!isset($item['layout'])) {
                    $item['layout'] = 'stacked';
                } elseif ($item['layout'] === 'image_left') {
                    $item['layout'] = 'image_left_50_50';
                } elseif ($item['layout'] === 'image_right') {
                    $item['layout'] = 'image_right_50_50';
                }
            }
            return $item;
        }, $data);
    }

    // ─── Relationships ────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_product_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'product_regions');
    }
}
