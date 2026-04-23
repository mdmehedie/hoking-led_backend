<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use App\Models\AppSetting;
use App\Helpers\TemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactSubmission $submission,
    ) {
    }

    public function envelope(): Envelope
    {
        $settings = AppSetting::first();
        $subject = 'New Contact Inquiry: ' . $this->submission->subject;

        if ($settings && !blank($settings->contact_internal_subject)) {
            $subject = TemplateHelper::parse($settings->contact_internal_subject, $this->submission);
        }

        return new Envelope(
            subject: $subject,
            replyTo: $this->submission->email,
        );
    }

    public function content(): Content
    {
        $settings = AppSetting::first();
        
        if ($settings && !blank($settings->contact_internal_template)) {
            $html = TemplateHelper::parse($settings->contact_internal_template, $this->submission);
            return new Content(
                htmlString: $html,
            );
        }

        return new Content(
            markdown: 'emails.admin.contact-notification',
            with: [
                'submission' => $this->submission,
            ],
        );
    }
}
