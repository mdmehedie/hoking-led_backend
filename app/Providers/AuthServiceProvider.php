<?php

namespace App\Providers;

use App\Models\AnalyticsEvent;
use App\Models\AppSetting;
use App\Models\Author;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\CaseStudy;
use App\Models\CaseStudyCategory;
use App\Models\Category;
use App\Models\CertificationAward;
use App\Models\ContactSubmission;
use App\Models\Content;
use App\Models\Conversation;
use App\Models\CoreAdvantage;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Locale;
use App\Models\News;
use App\Models\NewsletterSubscription;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Region;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\UiTranslation;
use App\Models\User;
use App\Models\Video;
use App\Policies\AnalyticsEventPolicy;
use App\Policies\AppSettingPolicy;
use App\Policies\AuthorPolicy;
use App\Policies\BlogPolicy;
use App\Policies\BrandPolicy;
use App\Policies\CaseStudyPolicy;
use App\Policies\CaseStudyCategoryPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CertificationAwardPolicy;
use App\Policies\ContactSubmissionPolicy;
use App\Policies\ContentPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\CoreAdvantagePolicy;
use App\Policies\FormPolicy;
use App\Policies\LeadPolicy;
use App\Policies\LocalePolicy;
use App\Policies\NewsPolicy;
use App\Policies\NewsletterSubscriptionPolicy;
use App\Policies\PagePolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RegionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SliderPolicy;
use App\Policies\TeamMemberPolicy;
use App\Policies\TestimonialPolicy;
use App\Policies\UiTranslationPolicy;
use App\Policies\UserPolicy;
use App\Policies\VideoPolicy;
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
        AnalyticsEvent::class => AnalyticsEventPolicy::class,
        AppSetting::class => AppSettingPolicy::class,
        Author::class => AuthorPolicy::class,
        Blog::class => BlogPolicy::class,
        Brand::class => BrandPolicy::class,
        CaseStudy::class => CaseStudyPolicy::class,
        CaseStudyCategory::class => CaseStudyCategoryPolicy::class,
        Category::class => CategoryPolicy::class,
        CertificationAward::class => CertificationAwardPolicy::class,
        ContactSubmission::class => ContactSubmissionPolicy::class,
        Content::class => ContentPolicy::class,
        Conversation::class => ConversationPolicy::class,
        CoreAdvantage::class => CoreAdvantagePolicy::class,
        Form::class => FormPolicy::class,
        Lead::class => LeadPolicy::class,
        Locale::class => LocalePolicy::class,
        News::class => NewsPolicy::class,
        NewsletterSubscription::class => NewsletterSubscriptionPolicy::class,
        Page::class => PagePolicy::class,
        Product::class => ProductPolicy::class,
        Project::class => ProjectPolicy::class,
        Region::class => RegionPolicy::class,
        Role::class => RolePolicy::class,
        Slider::class => SliderPolicy::class,
        TeamMember::class => TeamMemberPolicy::class,
        Testimonial::class => TestimonialPolicy::class,
        UiTranslation::class => UiTranslationPolicy::class,
        User::class => UserPolicy::class,
        Video::class => VideoPolicy::class,
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
