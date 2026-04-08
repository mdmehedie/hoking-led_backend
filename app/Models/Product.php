<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasTranslations, HasMedia, HasSeo;

    // ─── Properties ───────────────────────────────────────────

    protected $translatable = [
        'title',
        'short_description',
        'detailed_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'features',
        'video_embeds',
    ];

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

    /**
     * Keys inside translatable fields that contain media files.
     * Uses dotted notation: {attribute}.*.{key} for repeater-like structures.
     */
    protected array $translatableMediaKeys = [
        'detailed_description.*.image',
        'video_embeds.*.video_file',
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'video_embeds' => 'array',
        'gallery' => 'array',
        'downloads' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_top' => 'boolean',
    ];

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
