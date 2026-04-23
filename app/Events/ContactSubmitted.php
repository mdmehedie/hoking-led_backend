<?php

namespace App\Events;

use App\Models\ContactSubmission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public ContactSubmission $submission
    ) {
    }
}
