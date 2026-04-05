<?php

namespace App\Filament\Admin\Resources\AnalyticsEventResource\Pages;

use App\Filament\Admin\Resources\AnalyticsEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnalyticsEvents extends ListRecords
{
    protected static string $resource = AnalyticsEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
