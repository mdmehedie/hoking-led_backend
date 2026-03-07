<?php

namespace App\Filament\Admin\Resources\AnalyticsEventResource\Pages;

use App\Filament\Admin\Resources\AnalyticsEventResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewAnalyticsEvent extends ViewRecord
{
    protected static string $resource = AnalyticsEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
