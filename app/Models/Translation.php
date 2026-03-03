<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    protected $fillable = [
        'translatable_id',
        'translatable_type',
        'locale',
        'attribute',
        'value',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
