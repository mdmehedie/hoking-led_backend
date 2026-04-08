<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationAward extends Model
{
    use HasFactory, HasMedia;

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

    protected array $mediaAttributes = [
        'image_path',
    ];
}
