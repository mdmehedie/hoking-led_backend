<?php

namespace App\Observers;

use App\Models\Page;
use App\Jobs\PublishToSocialMedia;
use Illuminate\Support\Facades\Log;

class PageObserver
{
    /**
     * Handle the Page "updated" event.
     * Check if the page was just published and dispatch social media job
     */
    public function updated(Page $page): void
    {
        // Check if the page was just published (status changed to 'published')
        if ($page->wasChanged('status') && $page->status === 'published') {
            Log::info('Page published, dispatching social media job', [
                'page_id' => $page->id,
                'title' => $page->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($page, 'page');
        }
    }

    /**
     * Handle the Page "created" event.
     * Check if the page is created as published and dispatch social media job
     */
    public function created(Page $page): void
    {
        // Check if the page was created as published
        if ($page->status === 'published') {
            Log::info('Page created as published, dispatching social media job', [
                'page_id' => $page->id,
                'title' => $page->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($page, 'page');
        }
    }
}
