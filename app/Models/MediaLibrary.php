<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaLibrary extends Model
{
    protected $table = 'media_library';

    protected $fillable = [
        'user_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'alt_text',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function delete(): bool
    {
        if ($this->file_path) {
            Storage::disk('public')->delete($this->file_path);
        }
        return parent::delete();
    }
}
