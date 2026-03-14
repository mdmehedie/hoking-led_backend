<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CoreWebVitalsService
{
    private $apiKey;
    private $provider;

    public function __construct()
    {
        $this->provider = config('analytics.vitals_provider', 'custom');
        $this->apiKey = config('analytics.pagespeed_api_key');
    }

    /**
     * Get Core Web Vitals data from Google PageSpeed API
     */
    public function getPageSpeedData(string $url): array
    {
        if ($this->provider !== 'pagespeed' || !$this->apiKey) {
            return [];
        }

        return Cache::tags(['core_web_vitals', 'pagespeed'])->remember(
            "pagespeed_{$url}", 
            86400, // 24 hours
            function () use ($url) {
                try {
                    $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed";
                    $response = Http::get($apiUrl, [
                        'url' => $url,
                        'key' => $this->apiKey,
                        'category' => ['PERFORMANCE', 'ACCESSIBILITY', 'BEST_PRACTICES', 'SEO'],
                        'strategy' => 'mobile'
                    ]);

                    if (!$response->successful()) {
                        Log::error('PageSpeed API error', ['url' => $url, 'response' => $response->json()]);
                        return [];
                    }

                    $data = $response->json();
                    return $this->formatPageSpeedData($data);
                } catch (\Exception $e) {
                    Log::error('PageSpeed API exception', ['url' => $url, 'error' => $e->getMessage()]);
                    return [];
                }
            }
        );
    }

    /**
     * Get Core Web Vitals from local analytics events
     */
    public function getLocalVitalsData(int $days = 7): array
    {
        return Cache::tags(['core_web_vitals', 'local'])->remember(
            "local_vitals_{$days}d", 
            3600,
            function () use ($days) {
                return [
                    'lcp' => $this->getMetricData('LCP', $days),
                    'cls' => $this->getMetricData('CLS', $days),
                    'inp' => $this->getMetricData('INP', $days),
                ];
            }
        );
    }

    /**
     * Get aggregated metric data
     */
    private function getMetricData(string $metric, int $days): array
    {
        $events = \App\Models\AnalyticsEvent::byName('core_web_vital')
            ->betweenDates(now()->subDays($days), now())
            ->get();

        $metricEvents = $events->filter(function ($event) use ($metric) {
            return isset($event->parameters['metric']) && $event->parameters['metric'] === $metric;
        });

        $values = $metricEvents->pluck('parameters')->map(function ($params) use ($metric) {
            return $params['value'] ?? 0;
        })->filter()->values();

        if ($values->isEmpty()) {
            return [
                'count' => 0,
                'average' => 0,
                'median' => 0,
                'p75' => 0,
                'p95' => 0,
                'good' => 0,
                'needs_improvement' => 0,
                'poor' => 0,
            ];
        }

        $sorted = $values->sort()->values();
        $count = $sorted->count();
        $average = round($sorted->avg(), 2);
        $median = $sorted->get(floor($count / 2));
        $p75 = $sorted->get(floor($count * 0.75));
        $p95 = $sorted->get(floor($count * 0.95));

        // Determine thresholds based on metric type
        $thresholds = $this->getThresholds($metric);
        $good = $sorted->filter(fn ($v) => $v <= $thresholds['good'])->count();
        $needsImprovement = $sorted->filter(fn ($v) => $v > $thresholds['good'] && $v <= $thresholds['needs_improvement'])->count();
        $poor = $sorted->filter(fn ($v) => $v > $thresholds['needs_improvement'])->count();

        return [
            'count' => $count,
            'average' => $average,
            'median' => $median,
            'p75' => $p75,
            'p95' => $p95,
            'good' => $good,
            'needs_improvement' => $needsImprovement,
            'poor' => $poor,
        ];
    }

    /**
     * Get Core Web Vitals thresholds
     */
    private function getThresholds(string $metric): array
    {
        $thresholds = [
            'LCP' => [
                'good' => 2500, // 2.5s
                'needs_improvement' => 4000, // 4.0s
            ],
            'CLS' => [
                'good' => 0.1,
                'needs_improvement' => 0.25,
            ],
            'INP' => [
                'good' => 200, // 200ms
                'needs_improvement' => 500, // 500ms
            ],
        ];

        return $thresholds[$metric] ?? ['good' => 0, 'needs_improvement' => 0];
    }

    /**
     * Format PageSpeed API response
     */
    private function formatPageSpeedData(array $data): array
    {
        if (!isset($data['loadingExperience'])) {
            return [];
        }

        $metrics = $data['loadingExperience']['metrics'] ?? [];
        $origin = $data['origin'] ?? '';

        return [
            'url' => $origin,
            'overall_score' => $data['lighthouseResult']['categories']['performance']['score'] ?? 0,
            'core_web_vitals' => [
                'lcp' => [
                    'value' => $metrics['LARGEST_CONTENTFUL_PAINT_MS']['percentile'] ?? 0,
                    'rating' => $this->getRating($metrics['LARGEST_CONTENTFUL_PAINT_MS']['category'] ?? 'UNKNOWN'),
                ],
                'fid' => [
                    'value' => $metrics['FIRST_INPUT_DELAY_MS']['percentile'] ?? 0,
                    'rating' => $this->getRating($metrics['FIRST_INPUT_DELAY_MS']['category'] ?? 'UNKNOWN'),
                ],
                'cls' => [
                    'value' => $metrics['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'] ?? 0,
                    'rating' => $this->getRating($metrics['CUMULATIVE_LAYOUT_SHIFT_SCORE']['category'] ?? 'UNKNOWN'),
                ],
            ],
            'opportunities' => $data['lighthouseResult']['audits'] ?? [],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get rating from category
     */
    private function getRating(string $category): string
    {
        return match($category) {
            'FAST' => 'good',
            'AVERAGE' => 'needs_improvement',
            'SLOW' => 'poor',
            default => 'unknown',
        };
    }

    /**
     * Get monitoring URLs from settings
     */
    public function getMonitoringUrls(): array
    {
        $urls = setting('analytics.monitoring_urls', '/');
        $baseUrl = config('app.url');

        if (empty($urls)) {
            return [$baseUrl];
        }

        $urlList = array_map('trim', explode(',', $urls));
        return array_map(fn ($url) => rtrim($baseUrl, '/') . '/' . ltrim($url, '/'), $urlList);
    }

    /**
     * Get all Core Web Vitals data for dashboard
     */
    public function getDashboardData(): array
    {
        $urls = $this->getMonitoringUrls();
        $allData = [];

        foreach ($urls as $url) {
            $urlData = [];
            
            // Get PageSpeed data if configured
            if ($this->provider === 'pagespeed') {
                $urlData['pagespeed'] = $this->getPageSpeedData($url);
            }
            
            // Get local tracking data
            $urlData['local'] = $this->getLocalVitalsData(7);
            
            $allData[] = [
                'url' => $url,
                'data' => $urlData,
            ];
        }

        return $allData;
    }
}
