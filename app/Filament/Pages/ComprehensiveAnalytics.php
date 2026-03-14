<?php

namespace App\Filament\Pages;

use App\Services\GA4Service;
use App\Services\CoreWebVitalsService;
use App\Models\AnalyticsEvent;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ComprehensiveAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.comprehensive-analytics';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Comprehensive Analytics';

    public function getHeading(): string
    {
        return 'Comprehensive Analytics';
    }

    public function getTrafficData(): array
    {
        try {
            $ga4Service = app(GA4Service::class);
            return [
                'sessions' => $ga4Service->getSessions(7),
                'page_views' => $ga4Service->getPageViews(7),
                'devices' => $ga4Service->getDeviceBreakdown(7),
                'sources' => $ga4Service->getTrafficSources(7, 10),
                'top_pages' => $ga4Service->getTopPages(7, 10),
            ];
        } catch (\Exception $e) {
            return $this->getEmptyTrafficData();
        }
    }

    public function getBehaviorData(): array
    {
        return Cache::remember('behavior_analytics', 300, function () {
            return [
                'events' => AnalyticsEvent::selectRaw('
                        event_name,
                        COUNT(*) as total_events,
                        COUNT(DISTINCT user_id) as unique_users,
                        AVG(CASE WHEN parameters LIKE "%duration%" THEN JSON_EXTRACT(parameters, "$.duration") ELSE NULL END) as avg_duration
                    ')
                    ->where('event_time', '>=', now()->subDays(7))
                    ->groupBy('event_name')
                    ->orderBy('total_events', 'desc')
                    ->limit(20)
                    ->get()
                    ->toArray(),
                'funnels' => $this->getFunnelData(),
                'user_paths' => $this->getUserPaths(),
            ];
        });
    }

    public function getPerformanceData(): array
    {
        try {
            $vitalsService = app(CoreWebVitalsService::class);
            return [
                'core_web_vitals' => $vitalsService->getLocalVitalsData(7),
                'pagespeed_data' => $vitalsService->getDashboardData(),
                'performance_trends' => $this->getPerformanceTrends(),
            ];
        } catch (\Exception $e) {
            return $this->getEmptyPerformanceData();
        }
    }

    public function getFunnelData(): array
    {
        // Example funnel - you can make this configurable
        return [
            [
                'name' => 'Purchase Funnel',
                'steps' => [
                    ['event_name' => 'page_view', 'page' => '/products', 'label' => 'View Products'],
                    ['event_name' => 'click', 'parameters' => ['element' => 'add_to_cart'], 'label' => 'Add to Cart'],
                    ['event_name' => 'page_view', 'page' => '/checkout', 'label' => 'View Checkout'],
                    ['event_name' => 'form_submit', 'page' => '/checkout', 'label' => 'Complete Purchase'],
                ],
                'conversion_rates' => [
                    ['step' => 1, 'rate' => 100, 'label' => 'Products Viewed'],
                    ['step' => 2, 'rate' => 45, 'label' => 'Added to Cart'],
                    ['step' => 3, 'rate' => 30, 'label' => 'Started Checkout'],
                    ['step' => 4, 'rate' => 15, 'label' => 'Completed Purchase'],
                ]
            ]
        ];
    }

    public function getUserPaths(): array
    {
        return AnalyticsEvent::byName('page_view')
            ->selectRaw('
                    parameters->>"$.path" as path,
                    COUNT(*) as views,
                    COUNT(DISTINCT user_id) as unique_users
                ')
            ->where('event_time', '>=', now()->subDays(7))
            ->groupBy('path')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getPerformanceTrends(): array
    {
        return Cache::remember('performance_trends', 3600, function () {
            $data = AnalyticsEvent::byName('core_web_vital')
                ->selectRaw('
                        DATE(event_time) as date,
                        parameters->>"$.metric" as metric,
                        AVG(CAST(parameters->>"$.value" AS DECIMAL(10,2))) as avg_value,
                        MIN(CAST(parameters->>"$.value" AS DECIMAL(10,2))) as min_value,
                        MAX(CAST(parameters->>"$.value" AS DECIMAL(10,2))) as max_value,
                        COUNT(*) as count
                    ')
                ->where('event_time', '>=', now()->subDays(30))
                ->groupBy('date', 'metric')
                ->orderBy('date', 'desc')
                ->get()
                ->groupBy('metric');

            $trends = [];
            foreach ($data as $metric => $values) {
                $trends[$metric] = [
                    'current_avg' => $values->first()->avg_value ?? 0,
                    'previous_avg' => $values->skip(1)->first()->avg_value ?? 0,
                    'trend' => $this->calculateTrend($values),
                    'daily_data' => $values->map(fn ($v) => [
                        'date' => $v->date,
                        'avg_value' => $v->avg_value,
                        'count' => $v->count,
                    ])->toArray(),
                ];
            }

            return $trends;
        });
    }

    private function calculateTrend($values): string
    {
        if ($values->count() < 2) {
            return 'stable';
        }

        $current = $values->first()->avg_value;
        $previous = $values->skip(1)->first()->avg_value;

        if ($current > $previous * 1.1) {
            return 'improving';
        } elseif ($current < $previous * 0.9) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    private function getEmptyTrafficData(): array
    {
        return [
            'sessions' => ['sessions' => 0, 'bounce_rate' => 0, 'engagement_rate' => 0],
            'page_views' => [],
            'devices' => [],
            'sources' => [],
            'top_pages' => [],
        ];
    }

    private function getEmptyPerformanceData(): array
    {
        return [
            'core_web_vitals' => [
                'lcp' => ['average' => 0, 'count' => 0],
                'cls' => ['average' => 0, 'count' => 0],
                'inp' => ['average' => 0, 'count' => 0],
            ],
            'pagespeed_data' => [],
            'performance_trends' => [],
        ];
    }
}
