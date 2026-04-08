<x-mail::message>
# Thank You, {{ $submission->name }}!

Thank you for contacting us. We have received your message and will get back to you as soon as possible.

## Your Submission

**Subject:** {{ $submission->subject }}

**Received:** {{ $submission->created_at->format('M j, Y g:i A') }}

@if($submission->hasResource())
**Regarding:** {{ $submission->getResourceLabel() }}
@endif

## What Happens Next?

Our team will review your inquiry and respond within **24 hours** during business days.

## Your Message

{{ $submission->message }}

@if(config('app.url'))
<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>
@endif

Best regards,<br>
{{ config('app.name') }} Team
</x-mail::message>
