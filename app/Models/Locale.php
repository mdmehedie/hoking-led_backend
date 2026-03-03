<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Locale extends Model
{
    protected $fillable = [
        'code',
        'name',
        'direction',
        'is_default',
        'is_active',
        'flag_path',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $locale): void {
            if ($locale->is_default) {
                DB::table('locales')
                    ->where('id', '!=', $locale->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public static function defaultCode(): string
    {
        return static::query()->where('is_default', true)->value('code')
            ?? config('app.locale', 'en');
    }

    public static function activeCodes(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->pluck('code')
            ->values()
            ->all();
    }
}
