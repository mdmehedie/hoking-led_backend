<?php

namespace App\Helpers;

use App\Models\ContactSubmission;

class TemplateHelper
{
    /**
     * Parse placeholders in a template string and convert relative URLs to absolute.
     */
    public static function parse(?string $template, ContactSubmission $submission): string
    {
        if (blank($template)) {
            return '';
        }

        $placeholders = [
            '{{name}}' => $submission->name,
            '{{email}}' => $submission->email,
            '{{phone}}' => $submission->phone ?? '',
            '{{company}}' => $submission->place ?? '',
            '{{country}}' => $submission->country ?? '',
            '{{subject}}' => $submission->subject,
            '{{message}}' => $submission->message,
            '{{source}}' => $submission->getSourceLabelAttribute(),
            '{{date}}' => $submission->created_at->format('Y-m-d H:i:s'),
        ];

        $parsed = str_replace(array_keys($placeholders), array_values($placeholders), $template);

        // Convert relative URLs (like images from TinyMCE) to absolute URLs
        return ContentUrlHelper::convertAllUrlsToAbsolute($parsed);
    }
}
