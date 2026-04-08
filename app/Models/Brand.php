<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasMedia;

class Brand extends Model
{
    use HasMedia;

    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'description',
        'sort_order',
        'is_active',
    ];

    protected array $mediaAttributes = [
        'logo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
