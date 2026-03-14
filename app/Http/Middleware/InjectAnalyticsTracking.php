<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class InjectAnalyticsTracking
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only inject tracking scripts for HTML responses in production
        if ($response->headers->get('content-type') && 
            str_contains($response->headers->get('content-type'), 'text/html') && 
            app()->environment('production')) {
            
            $analyticsData = $this->getAnalyticsData();
            
            if (!empty($analyticsData)) {
                $content = $response->getContent();
                
                // Inject analytics configuration
                $configScript = $this->generateConfigScript($analyticsData);
                $trackerScript = $this->generateTrackerScript($analyticsData);
                $heatmapScript = $this->generateHeatmapScript($analyticsData);
                
                // Insert scripts before closing body tag
                $pattern = '/<\/body>/i';
                $replacement = $configScript . "\n" . $trackerScript . "\n" . $heatmapScript . "\n</body>";
                
                $content = preg_replace($pattern, $replacement, $content);
                $response->setContent($content);
            }
        }

        return $response;
    }

    private function getAnalyticsData(): array
    {
        return [
            'ga4_enabled' => setting('analytics.ga4_enabled', false),
            'heatmap_provider' => setting('analytics.heatmap_provider', 'none'),
            'hotjar_id' => setting('analytics.hotjar_id'),
            'clarity_id' => setting('analytics.clarity_id'),
            'track_page_views' => setting('analytics.track_page_views', true),
            'track_clicks' => setting('analytics.track_clicks', true),
            'track_forms' => setting('analytics.track_forms', true),
            'track_scrolling' => setting('analytics.track_scrolling', true),
            'track_core_web_vitals' => setting('analytics.track_core_web_vitals', true),
            'debug_mode' => setting('analytics.debug_mode', false),
        ];
    }

    private function generateConfigScript(array $data): string
    {
        $config = [
            'enableAutoTracking' => true,
            'trackPageViews' => $data['track_page_views'],
            'trackClicks' => $data['track_clicks'],
            'trackForms' => $data['track_forms'],
            'trackScrolling' => $data['track_scrolling'],
            'debug' => $data['debug_mode'],
        ];

        return "<script>window.analyticsConfig = " . json_encode($config) . ";</script>";
    }

    private function generateTrackerScript(array $data): string
    {
        if (!$data['track_page_views'] && !$data['track_clicks'] && 
            !$data['track_forms'] && !$data['track_scrolling']) {
            return '';
        }

        return '<script src="/js/analytics-tracker.js"></script>';
    }

    private function generateHeatmapScript(array $data): string
    {
        $scripts = [];

        // Hotjar
        if ($data['heatmap_provider'] === 'hotjar' && $data['hotjar_id']) {
            $scripts[] = "
                (function(h,o,t,j,a,r,l,y){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
                h._hjSettings={hjid:{$data['hotjar_id']},hjsv:6};
                a=o.getElementsByTagName('head')[0];
                r=o.createElement('script');r.async=1;
                r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
                a.appendChild(r);
            })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
            ";
        }

        // Microsoft Clarity
        if ($data['heatmap_provider'] === 'clarity' && $data['clarity_id']) {
            $scripts[] = "
                (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                c[l]=c[l]||function(){(c[l].q=c[l].q||[]).push(arguments)};
                t=l.createElement('script');t.async=1;t.src='https://www.clarity.ms/tag/'+i;
                y=l.getElementsByTagName('script')[0];y.parentNode.insertBefore(t,y);
                })(window,document,'clarity','script','clarity','{$data['clarity_id']}');
            ";
        }

        return empty($scripts) ? '' : '<script>' . implode('', $scripts) . '</script>';
    }
}
