<x-mail::message>
# New Contact Inquiry

A new contact submission has been received.

## Submission Details

**Name:** {{ $submission->name }}

**Email:** {{ $submission->email }}

**Phone:** {{ $submission->phone ?? 'Not provided' }}

**Place:** {{ $submission->place ?? 'Not provided' }}

**Subject:** {{ $submission->subject }}

**Source:** {{ $submission->source_label }}

**Priority:** {{ ucfirst($submission->priority) }}

**Submitted At:** {{ $submission->created_at->format('M j, Y g:i A') }}

@if($submission->hasResource())
**Related To:** {{ $submission->getResourceLabel() }}
@endif

## Message

{{ $submission->message }}

@if($submission->ip_address)
**IP Address:** {{ $submission->ip_address }}
@endif

<x-mail::button :url="url('/admin/contact-submissions/' . $submission->id)">
View in Admin Panel
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
