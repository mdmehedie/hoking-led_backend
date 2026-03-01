<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\AppSetting;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        try{
            $setting = AppSetting::first();
            if ($setting && $setting->sitemap_enabled) {
                $schedule->command('app:generate-sitemap')->daily();
            }
        }catch(\Exception $e){
            // Log the exception or handle it as needed
            \Log::error('Error scheduling sitemap generation: ' . $e->getMessage());
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
