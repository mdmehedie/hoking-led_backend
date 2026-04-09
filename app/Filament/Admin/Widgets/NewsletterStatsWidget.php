<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\NewsletterSubscriptionResource;
use App\Models\NewsletterSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsletterStatsWidget extends BaseWidget
{
    protected static ?int $sort = -7;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 'full',
    ];

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $totalSubscribers = NewsletterSubscription::count();
        $activeSubscribers = NewsletterSubscription::active()->count();
        $newThisMonth = NewsletterSubscription::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Subscribers', $totalSubscribers)
                ->description('All-time newsletter subscriptions')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('primary')
                ->url(NewsletterSubscriptionResource::getUrl()),
            Stat::make('Active Subscribers', $activeSubscribers)
                ->description('Currently active subscriptions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(NewsletterSubscriptionResource::getUrl()),
            Stat::make('New This Month', $newThisMonth)
                ->description('Subscriptions this month')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->url(NewsletterSubscriptionResource::getUrl()),
        ];
    }
}
