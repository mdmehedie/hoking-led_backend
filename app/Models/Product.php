<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'short_description',
        'detailed_description',
        'status',
        'published_at',
        'technical_specs',
        'tags',
        'category_id',
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getDetailedDescriptionAttribute($value)
    {
        if (is_string($value) && $decoded = json_decode($value, true)) {
            if (is_array($decoded) && isset($decoded['en'])) {
                return $decoded['en'];
            }
        }
        return $value;
    }
}
