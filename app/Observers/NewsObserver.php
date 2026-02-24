<?php

namespace App\Observers;

use App\Models\News;
use App\Jobs\PublishToSocialMedia;
use Illuminate\Support\Facades\Log;

class NewsObserver
{
    /**
     * Handle the News "updated" event.
     * Check if the news was just published and dispatch social media job
     */
    public function updated(News $news): void
    {
        // Check if the news was just published (status changed to 'published')
        if ($news->wasChanged('status') && $news->status === 'published') {
            Log::info('News published, dispatching social media job', [
                'news_id' => $news->id,
                'title' => $news->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($news, 'news');
        }
    }

    /**
     * Handle the News "created" event.
     * Check if the news is created as published and dispatch social media job
     */
    public function created(News $news): void
    {
        // Check if the news was created as published
        if ($news->status === 'published') {
            Log::info('News created as published, dispatching social media job', [
                'news_id' => $news->id,
                'title' => $news->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($news, 'news');
        }
    }
}
