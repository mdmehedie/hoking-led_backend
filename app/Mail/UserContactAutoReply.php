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

class UserContactAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactSubmission $submission,
    ) {
    }

    public function envelope(): Envelope
    {
        $settings = AppSetting::first();
        $subject = 'Thank you for contacting us';

        if ($settings && !blank($settings->contact_external_subject)) {
            $subject = TemplateHelper::parse($settings->contact_external_subject, $this->submission);
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $settings = AppSetting::first();
        
        if ($settings && !blank($settings->contact_external_template)) {
            $html = TemplateHelper::parse($settings->contact_external_template, $this->submission);
            return new Content(
                htmlString: $html,
            );
        }

        return new Content(
            markdown: 'emails.user.contact-auto-reply',
            with: [
                'submission' => $this->submission,
            ],
        );
    }
}
