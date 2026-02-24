<?php

namespace App\Observers;

use App\Models\Blog;
use App\Jobs\PublishToSocialMedia;
use Illuminate\Support\Facades\Log;

class BlogObserver
{
    /**
     * Handle the Blog "updated" event.
     * Check if the blog was just published and dispatch social media job
     */
    public function updated(Blog $blog): void
    {
        // Check if the blog was just published (status changed to 'published')
        if ($blog->wasChanged('status') && $blog->status === 'published') {
            Log::info('Blog published, dispatching social media job', [
                'blog_id' => $blog->id,
                'title' => $blog->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($blog, 'blog');
        }
    }

    /**
     * Handle the Blog "created" event.
     * Check if the blog is created as published and dispatch social media job
     */
    public function created(Blog $blog): void
    {
        // Check if the blog was created as published
        if ($blog->status === 'published') {
            Log::info('Blog created as published, dispatching social media job', [
                'blog_id' => $blog->id,
                'title' => $blog->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($blog, 'blog');
        }
    }
}
