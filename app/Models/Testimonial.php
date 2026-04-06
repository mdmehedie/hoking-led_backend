<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory, HasTranslations, HasMedia;

    protected $translatable = [
        'client_name',
        'client_position',
        'client_company',
        'testimonial',
    ];

    protected $fillable = [
        'client_name',
        'client_position',
        'client_company',
        'testimonial',
        'rating',
        'image_path',
        'is_visible',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected array $mediaAttributes = [
        'image_path',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sort_order)) {
                $model->sort_order = static::max('sort_order') + 1;
            }
        });
    }

    public function getImageAttribute()
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
