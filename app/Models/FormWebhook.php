<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormWebhook extends Model
{
    protected $fillable = [
        'form_id',
        'url',
        'method',
        'headers',
        'active',
    ];

    protected $casts = [
        'headers' => 'array',
        'active' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
