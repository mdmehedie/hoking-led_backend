<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SocialAccount extends Model
{
    protected $fillable = [
        'platform',
        'account_name',
        'credentials',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the platform display name
     */
    protected function platformDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->platform) {
                'facebook' => 'Facebook',
                'twitter' => 'Twitter (X)',
                'linkedin' => 'LinkedIn',
                default => ucfirst($this->platform),
            }
        );
    }

    /**
     * Get active social accounts for a specific platform
     */
    public static function activeForPlatform(string $platform): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('platform', $platform)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get all active social accounts
     */
    public static function active(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)->get();
    }

    /**
     * Get platform icon for UI display
     */
    public function getPlatformIcon(): string
    {
        return match($this->platform) {
            'facebook' => 'heroicon-o-facebook',
            'twitter' => 'heroicon-o-twitter',
            'linkedin' => 'heroicon-o-linkedin',
            default => 'heroicon-o-share',
        };
    }

    /**
     * Get platform color for UI display
     */
    public function getPlatformColor(): string
    {
        return match($this->platform) {
            'facebook' => 'blue',
            'twitter' => 'black',
            'linkedin' => 'blue',
            default => 'gray',
        };
    }
}
