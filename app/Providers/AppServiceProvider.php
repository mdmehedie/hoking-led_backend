<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Translations\DatabaseTranslationLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new DatabaseTranslationLoader($loader);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        // Custom validation rule for slugs without spaces
        \Validator::extend('no_spaces', function ($attribute, $value, $parameters, $validator) {
            return !str_contains($value, ' ');
        });

        \Validator::replacer('no_spaces', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute field cannot contain spaces. Use hyphens (-) instead.');
        });

        
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
