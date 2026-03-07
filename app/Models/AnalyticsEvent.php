<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'page',
        'url',
        'user_agent',
        'ip_address',
        'parameters',
        'user_id',
        'event_time',
    ];

    protected $casts = [
        'parameters' => 'array',
        'event_time' => 'datetime',
    ];

    /**
     * Get the user that owns the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include events of a given name.
     */
    public function scopeByName($query, $eventName)
    {
        return $query->where('event_name', $eventName);
    }

    /**
     * Scope a query to only include events from a given page.
     */
    public function scopeByPage($query, $page)
    {
        return $query->where('page', $page);
    }

    /**
     * Scope a query to only include events within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('event_time', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include events for a given user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted parameters as string.
     */
    public function getFormattedParametersAttribute(): string
    {
        if (!$this->parameters) {
            return '';
        }

        $formatted = [];
        foreach ($this->parameters as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }

        return implode(', ', $formatted);
    }

    /**
     * Get user agent parsed information.
     */
    public function getUserAgentInfoAttribute(): array
    {
        if (!$this->user_agent) {
            return [];
        }

        // Simple user agent parsing (you could use a library like jenssegers/agent)
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad/', $this->user_agent);
        $isBot = preg_match('/bot|crawler|spider|crawling/i', $this->user_agent);

        return [
            'is_mobile' => $isMobile,
            'is_bot' => $isBot,
            'browser' => $this->extractBrowser(),
            'platform' => $this->extractPlatform(),
        ];
    }

    /**
     * Extract browser name from user agent.
     */
    private function extractBrowser(): string
    {
        if (preg_match('/Chrome/', $this->user_agent)) return 'Chrome';
        if (preg_match('/Firefox/', $this->user_agent)) return 'Firefox';
        if (preg_match('/Safari/', $this->user_agent)) return 'Safari';
        if (preg_match('/Edge/', $this->user_agent)) return 'Edge';
        
        return 'Unknown';
    }

    /**
     * Extract platform from user agent.
     */
    private function extractPlatform(): string
    {
        if (preg_match('/Windows/', $this->user_agent)) return 'Windows';
        if (preg_match('/Mac/', $this->user_agent)) return 'macOS';
        if (preg_match('/Linux/', $this->user_agent)) return 'Linux';
        if (preg_match('/Android/', $this->user_agent)) return 'Android';
        if (preg_match('/iPhone|iPad/', $this->user_agent)) return 'iOS';
        
        return 'Unknown';
    }
}
