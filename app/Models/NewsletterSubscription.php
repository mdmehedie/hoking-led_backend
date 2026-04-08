<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'source',
        'status',
        'consent_given',
        'consent_ip',
        'subscribed_at',
        'unsubscribed_at',
        'last_activity_at',
        'unsubscribe_token',
        'preferences',
    ];

    protected $casts = [
        'consent_given' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'preferences' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->unsubscribe_token)) {
                $subscription->unsubscribe_token = Str::uuid();
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    public function scopeBounced($query)
    {
        return $query->where('status', 'bounced');
    }

    public function scopeConsented($query)
    {
        return $query->where('consent_given', true);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function markAsActive(): void
    {
        $this->update([
            'status' => 'active',
            'subscribed_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function markAsBounced(): void
    {
        $this->update([
            'status' => 'bounced',
            'unsubscribed_at' => now(),
        ]);
    }

    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }
}
