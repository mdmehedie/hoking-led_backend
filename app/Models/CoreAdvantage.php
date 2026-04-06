<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CoreAdvantage extends Model
{
    use HasFactory, HasTranslations, HasMedia;

    protected $table = 'core_advantages';

    protected $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'title',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected array $mediaAttributes = [
        'icon',
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
