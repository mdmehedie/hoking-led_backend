<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = [
        'name',
        'fields',
        'success_message',
        'email_notifications',
        'notification_emails',
        'store_leads',
    ];

    protected $casts = [
        'fields' => 'array',
        'notification_emails' => 'array',
        'email_notifications' => 'boolean',
        'store_leads' => 'boolean',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
