<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Services\GA4Service;

class PageViewsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $ga4Service = new GA4Service();
            $pageViews = $ga4Service->getPageViewsLast7Days();

            return [
                Stat::make('Page Views (Last 7 Days)', number_format($pageViews))
                    ->description('Total page views from Google Analytics 4')
                    ->descriptionIcon('heroicon-m-eye')
                    ->color('primary'),
            ];
        } catch (\Exception $e) {
            return [
                Stat::make('Page Views (Last 7 Days)', 'Not configured')
                    ->description('Configure GA4 credentials in Settings')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }
    }
}
