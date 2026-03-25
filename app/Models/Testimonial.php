<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSeo;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

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

        static::updating(function ($model) {
            // Delete old image if it's being replaced
            if ($model->isDirty('image_path') && $model->getOriginal('image_path')) {
                Storage::disk('public')->delete($model->getOriginal('image_path'));
            }
        });

        static::deleting(function ($model) {
            if ($model->image_path) {
                Storage::disk('public')->delete($model->image_path);
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
