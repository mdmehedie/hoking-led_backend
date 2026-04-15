<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'user_id',
        'message',
        'is_internal',
        'attachment_path',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    protected $appends = ['sender_name', 'is_admin'];

    // ─── Relationships ─────────────────────────────────────

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ─────────────────────────────────────────────

    public function scopeFromVisitor($query)
    {
        return $query->where('sender_type', 'visitor');
    }

    public function scopeFromAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }

    public function scopeVisibleToVisitor($query)
    {
        return $query->where('is_internal', false);
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->sender_type === 'admin';
    }

    public function getSenderNameAttribute(): string
    {
        if ($this->is_admin) {
            return $this->user?->name ?? 'Admin';
        }

        return $this->conversation->visitor_name ?? 'Visitor';
    }

    protected static function booted()
    {
        static::created(function ($message) {
            // When a new message is created, notify admins
            $conversation = $message->conversation;

            // Don't notify if it's an internal note (admin-only)
            if (!$message->is_admin) {
                $conversation->notifyAdminsAboutNewMessage(
                    $message->message,
                    $message->sender_type === 'visitor' ? $conversation->visitor_name : null
                );
            }
        });
    }
}
