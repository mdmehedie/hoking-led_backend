<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Filament\Admin\Resources\BlogResource;
use App\Filament\Admin\Resources\CaseStudyResource;
use App\Filament\Admin\Resources\NewsResource;
use App\Filament\Admin\Resources\PageResource;
use App\Filament\Admin\Resources\ProductResource;
use App\Filament\Admin\Resources\ProjectResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentCountsWidget extends BaseWidget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 'full',
    ];

    protected function getColumns(): int
    {
        return 6;
    }

    protected function getStats(): array
    {
        $blogCount = Blog::count();
        $productCount = Product::count();
        $pageCount = Page::count();
        $caseStudyCount = CaseStudy::count();
        $newsCount = News::count();
        $projectCount = Project::count();

        return [
            Stat::make('Blogs', $blogCount)
                ->description('Total blog posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->url(BlogResource::getUrl()),
            Stat::make('Products', $productCount)
                ->description('Total products')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->url(ProductResource::getUrl()),
            Stat::make('Pages', $pageCount)
                ->description('Total pages')
                ->descriptionIcon('heroicon-m-document')
                ->color('warning')
                ->url(PageResource::getUrl()),
            Stat::make('Case Studies', $caseStudyCount)
                ->description('Total case studies')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info')
                ->url(CaseStudyResource::getUrl()),
            Stat::make('News', $newsCount)
                ->description('Total news articles')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('danger')
                ->url(NewsResource::getUrl()),
            Stat::make('Projects', $projectCount)
                ->description('Total projects')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('indigo')
                ->url(ProjectResource::getUrl()),
        ];
    }
}
