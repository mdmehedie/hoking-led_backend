<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationAward extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'issuing_organization',
        'date_awarded',
        'description',
        'image_path',
        'is_visible',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'date_awarded' => 'date',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = \Str::slug($model->title);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && empty($model->slug)) {
                $model->slug = \Str::slug($model->title);
            }
        });
    }

    public function getImageAttribute()
    {
        return $this->image_path ? asset('storage/certifications/' . $this->image_path) : null;
    }
}
