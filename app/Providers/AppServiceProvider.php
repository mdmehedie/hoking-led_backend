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
        // Register model observers
        \App\Models\Blog::observe(\App\Observers\BlogObserver::class);
        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
    }
}
