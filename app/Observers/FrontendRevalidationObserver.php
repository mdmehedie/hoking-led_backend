<?php

namespace App\Observers;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\CaseStudy;
use App\Models\CaseStudyCategory;
use App\Models\Category;
use App\Models\CertificationAward;
use App\Models\CoreAdvantage;
use App\Models\Locale;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Video;
use App\Services\FrontendRevalidationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;

class FrontendRevalidationObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Tag names must match the cache tags the Next.js frontend attaches to its API fetches.
     *
     * @var array<class-string<Model>, array<int, string>>
     */
    public const TAGS = [
        Product::class => ['products'],
        // Product responses embed the category name and slug.
        Category::class => ['categories', 'products'],
        Slider::class => ['sliders'],
        Brand::class => ['brands'],
        Testimonial::class => ['testimonials'],
        CoreAdvantage::class => ['core-advantages'],
        Blog::class => ['blogs'],
        News::class => ['news'],
        Project::class => ['projects'],
        CaseStudy::class => ['case-studies'],
        CaseStudyCategory::class => ['case-studies'],
        Video::class => ['videos'],
        Page::class => ['pages'],
        TeamMember::class => ['team-members'],
        CertificationAward::class => ['certifications'],
        Locale::class => ['languages'],
    ];

    public function saved(Model $model): void
    {
        $this->revalidate($model);
    }

    public function deleted(Model $model): void
    {
        $this->revalidate($model);
    }

    protected function revalidate(Model $model): void
    {
        $tags = self::TAGS[$model::class] ?? [];

        if ($tags) {
            FrontendRevalidationService::revalidate($tags);
        }
    }
}
