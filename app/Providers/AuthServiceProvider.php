<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\Author;
use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\CertificationAward;
use App\Models\Content;
use App\Models\Form;
use App\Models\Lead;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\User;
use App\Policies\AppSettingPolicy;
use App\Policies\AuthorPolicy;
use App\Policies\BlogPolicy;
use App\Policies\CaseStudyPolicy;
use App\Policies\CertificationAwardPolicy;
use App\Policies\ContentPolicy;
use App\Policies\FormPolicy;
use App\Policies\LeadPolicy;
use App\Policies\NewsPolicy;
use App\Policies\PagePolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use App\Policies\SliderPolicy;
use App\Policies\TestimonialPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AppSetting::class => AppSettingPolicy::class,
        Author::class => AuthorPolicy::class,
        Blog::class => BlogPolicy::class,
        CaseStudy::class => CaseStudyPolicy::class,
        CertificationAward::class => CertificationAwardPolicy::class,
        Content::class => ContentPolicy::class,
        Form::class => FormPolicy::class,
        Lead::class => LeadPolicy::class,
        News::class => NewsPolicy::class,
        Page::class => PagePolicy::class,
        Product::class => ProductPolicy::class,
        Role::class => RolePolicy::class,
        Slider::class => SliderPolicy::class,
        Testimonial::class => TestimonialPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
