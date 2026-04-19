<?php

namespace App\Filament\Admin\Resources\VideoResource\Pages;

use App\Filament\Admin\Resources\VideoResource;
use Filament\Resources\Pages\EditRecord;

class EditVideo extends EditRecord
{
    protected static string $resource = VideoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
