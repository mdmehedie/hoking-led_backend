<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\ApiCore\ApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GA4Service
{
    private $client;
    private $propertyId;

    public function __construct()
    {
        $this->propertyId = config('services.ga4.property_id');
        $credentialsPath = config('services.ga4.credentials_path');

        if (!$this->propertyId || !$credentialsPath) {
            throw new \Exception('GA4 credentials not configured');
        }

        try {
            $this->client = new BetaAnalyticsDataClient([
                'credentials' => $credentialsPath,
            ]);
        } catch (\Exception $e) {
            Log::error('GA4 client initialization failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get page views for the last N days
     */
    public function getPageViews(int $days = 7): array
    {
        return Cache::tags(['ga4_analytics', 'page_views'])->remember("ga4_page_views_{$days}d", 3600, function () use ($days) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate("{$days}daysAgo")
                            ->setEndDate('yesterday')
                    ])
                    ->setDimensions([new Dimension(['name' => 'date'])])
                    ->setMetrics([new Metric(['name' => 'screenPageViews'])]);

                $response = $this->client->runReport($request);
                
                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'date' => $row->getDimensionValues()[0]->getValue(),
                        'page_views' => $row->getMetricValues()[0]->getValue(),
                    ];
                }

                return $data;
            } catch (ApiException $e) {
                Log::error('GA4 API error fetching page views', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get sessions data
     */
    public function getSessions(int $days = 7): array
    {
        return Cache::tags(['ga4_analytics', 'sessions'])->remember("ga4_sessions_{$days}d", 3600, function () use ($days) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate("{$days}daysAgo")
                            ->setEndDate('yesterday')
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'bounceRate']),
                        new Metric(['name' => 'engagementRate']),
                    ]);

                $response = $this->client->runReport($request);
                
                if (empty($response->getRows())) {
                    return [
                        'sessions' => 0,
                        'bounce_rate' => 0,
                        'engagement_rate' => 0,
                    ];
                }

                $row = $response->getRows()[0];
                return [
                    'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                    'bounce_rate' => round((float) $row->getMetricValues()[1]->getValue(), 2),
                    'engagement_rate' => round((float) $row->getMetricValues()[2]->getValue(), 2),
                ];
            } catch (ApiException $e) {
                Log::error('GA4 API error fetching sessions', ['error' => $e->getMessage()]);
                return [
                    'sessions' => 0,
                    'bounce_rate' => 0,
                    'engagement_rate' => 0,
                ];
            }
        });
    }

    /**
     * Get top pages
     */
    public function getTopPages(int $days = 7, int $limit = 10): array
    {
        return Cache::tags(['ga4_analytics', 'top_pages'])->remember("ga4_top_pages_{$days}d_{$limit}", 3600, function () use ($days, $limit) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate("{$days}daysAgo")
                            ->setEndDate('yesterday')
                    ])
                    ->setDimensions([new Dimension(['name' => 'pagePath'])])
                    ->setMetrics([new Metric(['name' => 'screenPageViews'])])
                    ->setOrderBys([
                        (new OrderBy())
                            ->setDimension('pagePath')
                            ->setOrderType('ORDER_TYPE_COUNT')
                            ->setDesc(true)
                    ])
                    ->setLimit($limit);

                $response = $this->client->runReport($request);
                
                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'page' => $row->getDimensionValues()[0]->getValue(),
                        'page_views' => (int) $row->getMetricValues()[0]->getValue(),
                    ];
                }

                return $data;
            } catch (ApiException $e) {
                Log::error('GA4 API error fetching top pages', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get device breakdown
     */
    public function getDeviceBreakdown(int $days = 7): array
    {
        return Cache::tags(['ga4_analytics', 'devices'])->remember("ga4_devices_{$days}d", 3600, function () use ($days) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate("{$days}daysAgo")
                            ->setEndDate('yesterday')
                    ])
                    ->setDimensions([new Dimension(['name' => 'deviceCategory'])])
                    ->setMetrics([new Metric(['name' => 'sessions'])])
                    ->setOrderBys([
                        (new OrderBy())
                            ->setMetric('sessions')
                            ->setOrderType('ORDER_TYPE_COUNT')
                            ->setDesc(true)
                    ]);

                $response = $this->client->runReport($request);
                
                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'device' => $row->getDimensionValues()[0]->getValue(),
                        'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                    ];
                }

                return $data;
            } catch (ApiException $e) {
                Log::error('GA4 API error fetching device breakdown', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get traffic sources
     */
    public function getTrafficSources(int $days = 7, int $limit = 10): array
    {
        return Cache::tags(['ga4_analytics', 'traffic_sources'])->remember("ga4_traffic_sources_{$days}d_{$limit}", 3600, function () use ($days, $limit) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate("{$days}daysAgo")
                            ->setEndDate('yesterday')
                    ])
                    ->setDimensions([new Dimension(['name' => 'sessionSource'])])
                    ->setMetrics([new Metric(['name' => 'sessions'])])
                    ->setOrderBys([
                        (new OrderBy())
                            ->setMetric('sessions')
                            ->setOrderType('ORDER_TYPE_COUNT')
                            ->setDesc(true)
                    ])
                    ->setLimit($limit);

                $response = $this->client->runReport($request);
                
                $data = [];
                foreach ($response->getRows() as $row) {
                    $data[] = [
                        'source' => $row->getDimensionValues()[0]->getValue(),
                        'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                    ];
                }

                return $data;
            } catch (ApiException $e) {
                Log::error('GA4 API error fetching traffic sources', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get real-time data
     */
    public function getRealTimeData(): array
    {
        return Cache::tags(['ga4_analytics', 'realtime'])->remember('ga4_realtime', 60, function () {
            try {
                // Note: This would require the Realtime Reporting API
                // For now, we'll return cached recent data
                return [
                    'active_users' => 0,
                    'active_pages' => 0,
                ];
            } catch (ApiException $e) {
                Log::error('GA4 API error fetching real-time data', ['error' => $e->getMessage()]);
                return [
                    'active_users' => 0,
                    'active_pages' => 0,
                ];
            }
        });
    }

    /**
     * Get key metrics for dashboard
     */
    public function getDashboardMetrics(int $days = 7): array
    {
        return Cache::tags(['ga4_analytics', 'dashboard'])->remember("ga4_dashboard_{$days}d", 3600, function () use ($days) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate("{$days}daysAgo")
                            ->setEndDate('yesterday')
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'totalUsers']),
                        new Metric(['name' => 'bounceRate']),
                        new Metric(['name' => 'engagementRate']),
                        new Metric(['name' => 'averageSessionDuration']),
                    ]);

                $response = $this->client->runReport($request);
                
                if (empty($response->getRows())) {
                    return $this->getEmptyMetrics();
                }

                $row = $response->getRows()[0];
                return [
                    'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                    'page_views' => (int) $row->getMetricValues()[1]->getValue(),
                    'users' => (int) $row->getMetricValues()[2]->getValue(),
                    'bounce_rate' => round((float) $row->getMetricValues()[3]->getValue(), 2),
                    'engagement_rate' => round((float) $row->getMetricValues()[4]->getValue(), 2),
                    'avg_session_duration' => round((float) $row->getMetricValues()[5]->getValue(), 2),
                ];
            } catch (ApiException $e) {
                Log::error('GA4 API error fetching dashboard metrics', ['error' => $e->getMessage()]);
                return $this->getEmptyMetrics();
            }
        });
    }

    /**
     * Get empty metrics structure
     */
    private function getEmptyMetrics(): array
    {
        return [
            'sessions' => 0,
            'page_views' => 0,
            'users' => 0,
            'bounce_rate' => 0,
            'engagement_rate' => 0,
            'avg_session_duration' => 0,
        ];
    }
}
