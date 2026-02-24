<?php

namespace App\Observers;

use App\Models\CaseStudy;
use App\Jobs\PublishToSocialMedia;
use Illuminate\Support\Facades\Log;

class CaseStudyObserver
{
    /**
     * Handle the CaseStudy "updated" event.
     * Check if the case study was just published and dispatch social media job
     */
    public function updated(CaseStudy $caseStudy): void
    {
        // Check if the case study was just published (status changed to 'published')
        if ($caseStudy->wasChanged('status') && $caseStudy->status === 'published') {
            Log::info('Case study published, dispatching social media job', [
                'case_study_id' => $caseStudy->id,
                'title' => $caseStudy->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($caseStudy, 'case_study');
        }
    }

    /**
     * Handle the CaseStudy "created" event.
     * Check if the case study is created as published and dispatch social media job
     */
    public function created(CaseStudy $caseStudy): void
    {
        // Check if the case study was created as published
        if ($caseStudy->status === 'published') {
            Log::info('Case study created as published, dispatching social media job', [
                'case_study_id' => $caseStudy->id,
                'title' => $caseStudy->title,
            ]);

            // Dispatch job to publish to all active social media accounts
            PublishToSocialMedia::dispatch($caseStudy, 'case_study');
        }
    }
}
