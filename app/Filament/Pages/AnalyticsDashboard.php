<?php

namespace App\Filament\Pages;

use App\Services\GA4Service;
use App\Models\AnalyticsEvent;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.analytics-dashboard';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Analytics Dashboard';

    public function getHeading(): string
    {
        return 'Analytics Dashboard';
    }

    public function getStats(): array
    {
        try {
            $ga4Service = app(GA4Service::class);
            $metrics = $ga4Service->getDashboardMetrics(7);

            return [
                Stat::make('Sessions', number_format($metrics['sessions']))
                    ->description('Last 7 days')
                    ->descriptionIcon('heroicon-m-calendar')
                    ->chart([7, 2, 10, 3, 15, 4, 6])
                    ->color('primary'),

                Stat::make('Page Views', number_format($metrics['page_views']))
                    ->description('Last 7 days')
                    ->descriptionIcon('heroicon-m-eye')
                    ->chart([6, 12, 8, 14, 10, 16, 18])
                    ->color('success'),

                Stat::make('Users', number_format($metrics['users']))
                    ->description('Last 7 days')
                    ->descriptionIcon('heroicon-m-users')
                    ->chart([3, 8, 5, 12, 7, 14, 9])
                    ->color('warning'),

                Stat::make('Bounce Rate', $metrics['bounce_rate'] . '%')
                    ->description('Last 7 days')
                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->chart([20, 15, 18, 12, 16, 14, 10])
                    ->color('danger'),
            ];
        } catch (\Exception $e) {
            return [
                Stat::make('Sessions', '0')
                    ->description('GA4 not configured')
                    ->color('primary'),

                Stat::make('Page Views', '0')
                    ->description('GA4 not configured')
                    ->color('success'),

                Stat::make('Users', '0')
                    ->description('GA4 not configured')
                    ->color('warning'),

                Stat::make('Bounce Rate', '0%')
                    ->description('GA4 not configured')
                    ->color('danger'),
            ];
        }
    }

    public function getTopPages(): array
    {
        try {
            $ga4Service = app(GA4Service::class);
            return $ga4Service->getTopPages(7, 5);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getDeviceBreakdown(): array
    {
        try {
            $ga4Service = app(GA4Service::class);
            return $ga4Service->getDeviceBreakdown(7);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getTrafficSources(): array
    {
        try {
            $ga4Service = app(GA4Service::class);
            return $ga4Service->getTrafficSources(7, 5);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCustomEvents(): array
    {
        return Cache::remember('custom_events_stats', 300, function () {
            return AnalyticsEvent::selectRaw('
                    event_name,
                    COUNT(*) as count,
                    COUNT(DISTINCT user_id) as unique_users
                ')
                ->where('event_time', '>=', now()->subDays(7))
                ->groupBy('event_name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->toArray();
        });
    }

    public function getEngagementRate(): array
    {
        try {
            $ga4Service = app(GA4Service::class);
            $metrics = $ga4Service->getDashboardMetrics(7);
            
            return [
                'engagement_rate' => $metrics['engagement_rate'],
                'avg_session_duration' => $metrics['avg_session_duration'],
            ];
        } catch (\Exception $e) {
            return [
                'engagement_rate' => 0,
                'avg_session_duration' => 0,
            ];
        }
    }
}
