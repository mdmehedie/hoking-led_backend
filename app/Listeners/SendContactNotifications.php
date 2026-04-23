<?php

namespace App\Listeners;

use App\Events\ContactSubmitted;
use App\Models\AppSetting;
use App\Models\User;
use App\Mail\AdminContactNotification;
use App\Mail\UserContactAutoReply;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendContactNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ContactSubmitted $event): void
    {
        $submission = $event->submission;
        
        $settings = AppSetting::first();
        
        if (!$settings) {
            return;
        }

        // 1. Internal Alerts (Staff)
        if ($settings->contact_internal_enabled) {
            $recipients = [];

            // Add specific recipients from settings
            if (!empty($settings->contact_internal_recipients)) {
                $recipients = array_merge($recipients, $settings->contact_internal_recipients);
            }

            $recipients = array_unique(array_filter($recipients));

            if (!empty($recipients)) {
                try {
                    Mail::to($recipients)->send(new AdminContactNotification($submission));
                } catch (\Exception $e) {
                    Log::error('Event Listener: Internal alert failed: ' . $e->getMessage());
                }
            }
        }

        // 2. External Acknowledgement (Visitor)
        if ($settings->contact_external_enabled && !empty($submission->email)) {
            try {
                Mail::to($submission->email)->send(new UserContactAutoReply($submission));
            } catch (\Exception $e) {
                Log::error('Event Listener: External auto-reply failed: ' . $e->getMessage());
            }
        }
    }
}
