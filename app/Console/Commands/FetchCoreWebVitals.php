<?php

namespace App\Console\Commands;

use App\Services\CoreWebVitalsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchCoreWebVitals extends Command
{
    protected $signature = 'analytics:fetch-core-web-vitals {--urls=*}';
    
    protected $description = 'Fetch Core Web Vitals data from monitoring services';

    public function handle()
    {
        $this->info('Starting Core Web Vitals data fetch...');

        try {
            $vitalsService = app(CoreWebVitalsService::class);
            $urls = $this->option('urls') ? explode(',', $this->option('urls')) : $vitalsService->getMonitoringUrls();

            foreach ($urls as $url) {
                $this->line("Fetching data for: {$url}");
                
                $data = $vitalsService->getPageSpeedData(trim($url));
                
                if (!empty($data)) {
                    $this->info("✓ Retrieved data for: {$url}");
                    $this->line("  Performance Score: " . round($data['overall_score'] ?? 0));
                    
                    if (isset($data['core_web_vitals'])) {
                        foreach ($data['core_web_vitals'] as $metric => $vital) {
                            $this->line("  {$metric}: {$vital['value']} ({$vital['rating']})");
                        }
                    }
                } else {
                    $this->warn("✗ No data retrieved for: {$url}");
                }
            }

            $this->info('Core Web Vitals data fetch completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Failed to fetch Core Web Vitals data: ' . $e->getMessage());
            Log::error('Core Web Vitals fetch command failed', ['error' => $e->getMessage()]);
            return 1;
        }

        return 0;
    }
}
