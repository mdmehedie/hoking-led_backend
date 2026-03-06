<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
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
     * Get page views for the last 7 days
     */
    public function getPageViewsLast7Days()
    {
        return Cache::tags(['ga4_analytics', 'page_views'])->remember('ga4_page_views_7d', 3600, function () {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate('7daysAgo')
                            ->setEndDate('yesterday')
                    ])
                    ->setMetrics([
                        (new Metric())->setName('screenPageViews')
                    ]);

                $response = $this->client->runReport($request);
                $rows = $response->getRows();

                if (empty($rows)) {
                    return 0;
                }

                return (int) $rows[0]->getMetricValues()[0]->getValue();
            } catch (ApiException $e) {
                Log::error('GA4 API error - Page views', ['error' => $e->getMessage()]);
                return 0;
            }
        });
    }

    /**
     * Get top visited pages/products (last 30 days)
     */
    public function getTopVisitedPages($limit = 10)
    {
        return Cache::tags(['ga4_analytics', 'top_pages'])->remember('ga4_top_pages_30d', 3600, function () use ($limit) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate('30daysAgo')
                            ->setEndDate('yesterday')
                    ])
                    ->setDimensions([
                        (new Dimension())->setName('pagePath')
                    ])
                    ->setMetrics([
                        (new Metric())->setName('screenPageViews')
                    ])
                    ->setOrderBys([
                        (new \Google\Analytics\Data\V1beta\OrderBy())
                            ->setMetric((new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy())
                                ->setMetricName('screenPageViews'))
                            ->setDesc(true)
                    ])
                    ->setLimit($limit);

                $response = $this->client->runReport($request);
                $rows = $response->getRows();

                $results = [];
                foreach ($rows as $row) {
                    $results[] = [
                        'page_path' => $row->getDimensionValues()[0]->getValue(),
                        'page_views' => (int) $row->getMetricValues()[0]->getValue()
                    ];
                }

                return $results;
            } catch (ApiException $e) {
                Log::error('GA4 API error - Top pages', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get traffic sources (last 30 days)
     */
    public function getTrafficSources($limit = 10)
    {
        return Cache::tags(['ga4_analytics', 'traffic_sources'])->remember('ga4_traffic_sources_30d', 3600, function () use ($limit) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setDateRanges([
                        (new DateRange())
                            ->setStartDate('30daysAgo')
                            ->setEndDate('yesterday')
                    ])
                    ->setDimensions([
                        (new Dimension())->setName('sessionDefaultChannelGrouping')
                    ])
                    ->setMetrics([
                        (new Metric())->setName('sessions'),
                        (new Metric())->setName('totalUsers')
                    ])
                    ->setOrderBys([
                        (new \Google\Analytics\Data\V1beta\OrderBy())
                            ->setMetric((new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy())
                                ->setMetricName('sessions'))
                            ->setDesc(true)
                    ])
                    ->setLimit($limit);

                $response = $this->client->runReport($request);
                $rows = $response->getRows();

                $results = [];
                foreach ($rows as $row) {
                    $results[] = [
                        'source' => $row->getDimensionValues()[0]->getValue(),
                        'sessions' => (int) $row->getMetricValues()[0]->getValue(),
                        'users' => (int) $row->getMetricValues()[1]->getValue()
                    ];
                }

                return $results;
            } catch (ApiException $e) {
                Log::error('GA4 API error - Traffic sources', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get real-time active users
     */
    public function getRealtimeUsers()
    {
        return Cache::tags(['ga4_analytics', 'realtime'])->remember('ga4_realtime_users', 60, function () {
            try {
                $request = (new \Google\Analytics\Data\V1beta\RunRealtimeReportRequest())
                    ->setProperty('properties/' . $this->propertyId)
                    ->setMetrics([
                        (new Metric())->setName('activeUsers')
                    ]);

                $response = $this->client->runRealtimeReport($request);
                $rows = $response->getRows();

                if (empty($rows)) {
                    return 0;
                }

                return (int) $rows[0]->getMetricValues()[0]->getValue();
            } catch (ApiException $e) {
                Log::error('GA4 API error - Realtime users', ['error' => $e->getMessage()]);
                return 0;
            }
        });
    }

    /**
     * Test GA4 connection
     */
    public function testConnection()
    {
        try {
            $this->getPageViewsLast7Days();
            return true;
        } catch (\Exception $e) {
            Log::error('GA4 connection test failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
