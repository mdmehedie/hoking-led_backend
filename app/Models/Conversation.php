<?php

namespace App\Models;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Conversation extends Model
{
    protected $fillable = [
        'session_id',
        'visitor_name',
        'visitor_email',
        'phone',
        'country',
        'company_name',
        'status',
        'priority',
        'assigned_to',
        'last_visitor_message_at',
        'last_admin_message_at',
        'admin_read_at',
        'resolved_at',
        'closed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'last_visitor_message_at' => 'datetime',
        'last_admin_message_at' => 'datetime',
        'admin_read_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($conversation) {
            $conversation->session_id = (string) Str::uuid();
        });
    }

    // ─── Relationships ─────────────────────────────────────

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // ─── Scopes ─────────────────────────────────────────────

    public function scopeNeedsResponse($query)
    {
        return $query->where('status', 'awaiting_admin');
    }

    public function scopeAwaitingVisitor($query)
    {
        return $query->where('status', 'awaiting_visitor');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeReopened($query)
    {
        return $query->where('status', 'reopened');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    // ─── Status Methods ─────────────────────────────────────

    public function visitorReply($message): Message
    {
        $message = $this->messages()->create([
            'sender_type' => 'visitor',
            'message' => $message,
        ]);

        $update = [
            'last_visitor_message_at' => now(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];

        if (in_array($this->status, ['resolved', 'closed'])) {
            $update['status'] = 'reopened';
        } elseif ($this->status === 'awaiting_visitor') {
            $update['status'] = 'awaiting_admin';
        } elseif ($this->status === 'new') {
            $update['status'] = 'awaiting_admin';
        }

        $this->update($update);

        return $message;
    }

    public function adminReply($message, $userId, $isInternal = false): void
    {
        $this->messages()->create([
            'sender_type' => 'admin',
            'user_id' => $userId,
            'message' => $message,
            'is_internal' => $isInternal,
        ]);

        $update = [
            'last_admin_message_at' => now(),
        ];

        if ($this->status !== 'closed') {
            $update['status'] = 'awaiting_visitor';
        }

        $this->update($update);
    }

    /**
     * Send notification to all admins about new message
     */
    public function notifyAdminsAboutNewMessage($message, $senderName = null)
    {
        // Get all admin users (assuming you have an 'is_admin' column)
        $admins = User::all();

        $visitorName = $senderName ?? $this->visitor_name;

        foreach ($admins as $admin) {
            Notification::make()
                ->title('New Message Received')
                ->body("{$visitorName} sent: " . substr($message, 0, 100))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->iconColor('primary')
                ->actions([
                    Action::make('view')
                        ->label('View Conversation')
                        ->url(route('filament.admin.resources.conversations.view', ['record' => $this]))
                        ->markAsRead(),
                ])
                ->sendToDatabase($admin);

            event(new DatabaseNotificationsSent($admin));
        }
    }

    /**
     * Send notification to admins about new conversation
     */
    public function notifyAdminsAboutNewConversation()
    {
        $admins = User::all();

        foreach ($admins as $admin) {
            Notification::make()
                ->title('New Conversation Started')
                ->body("{$this->visitor_name} ({$this->visitor_email}) started a new conversation")
                ->icon('heroicon-o-user-plus')
                ->iconColor('success')
                ->actions([
                    Action::make('view')
                        ->label('View Conversation')
                        ->url(route('filament.admin.resources.conversations.view', ['record' => $this]))
                        ->markAsRead(),
                ])
                ->sendToDatabase($admin);

            event(new DatabaseNotificationsSent($admin));
        }
    }

    public function markAsResolved(): void
    {
        $this->update(['status' => 'resolved', 'resolved_at' => now()]);
    }

    public function markAsClosed(): void
    {
        $this->update(['status' => 'closed', 'closed_at' => now()]);
    }

    public function markAsReopened(): void
    {
        $this->update(['status' => 'reopened']);
    }

    public function assignTo($userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    public function markAdminRead(): void
    {
        $this->update(['admin_read_at' => now()]);
    }

    public function hasUnreadFromVisitor(): bool
    {
        return $this->last_visitor_message_at &&
            (!$this->admin_read_at || $this->last_visitor_message_at > $this->admin_read_at);
    }

    public function getUnreadCountAttribute(): int
    {
        if (!$this->admin_read_at && !$this->last_admin_message_at) {
            return 0;
        }

        $query = $this->messages()->where('sender_type', 'visitor');

        if ($this->admin_read_at) {
            $query->where('created_at', '>', $this->admin_read_at);
        }

        return $query->count();
    }

    // ─── Helper Methods ─────────────────────────────────────

    public function getLastSenderAttribute(): ?string
    {
        if (!$this->last_visitor_message_at && !$this->last_admin_message_at) {
            return null;
        }

        if (!$this->last_visitor_message_at) {
            return 'admin';
        }

        if (!$this->last_admin_message_at) {
            return 'visitor';
        }

        return $this->last_visitor_message_at > $this->last_admin_message_at ? 'visitor' : 'admin';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'info',
            'awaiting_admin' => 'warning',
            'awaiting_visitor' => 'success',
            'resolved' => 'info',
            'closed' => 'gray',
            'reopened' => 'danger',
            default => 'gray',
        };
    }
}
