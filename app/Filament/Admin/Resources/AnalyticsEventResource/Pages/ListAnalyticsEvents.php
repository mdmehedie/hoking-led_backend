<?php

namespace App\Filament\Admin\Resources\AnalyticsEventResource\Pages;

use App\Filament\Admin\Resources\AnalyticsEventResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListAnalyticsEvents extends ListRecords
{
    protected static string $resource = AnalyticsEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
