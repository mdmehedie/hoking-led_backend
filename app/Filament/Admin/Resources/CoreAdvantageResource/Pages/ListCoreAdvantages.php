<?php

namespace App\Filament\Admin\Resources\CoreAdvantageResource\Pages;

use App\Filament\Admin\Resources\CoreAdvantageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCoreAdvantages extends ListRecords
{
    protected static string $resource = CoreAdvantageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
