<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Register model observers
            \App\Models\Blog::observe(\App\Observers\BlogObserver::class);
            \App\Models\Product::observe(\App\Observers\ProductObserver::class);
            \App\Models\Page::observe(\App\Observers\PageObserver::class);
            \App\Models\News::observe(\App\Observers\NewsObserver::class);
            \App\Models\CaseStudy::observe(\App\Observers\CaseStudyObserver::class);
        }  catch(\Exception $e){
            
        }
    }
}
