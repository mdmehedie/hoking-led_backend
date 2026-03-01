<?php

namespace App\Filament\Admin\Resources\CertificationAwardResource\Pages;

use App\Filament\Admin\Resources\CertificationAwardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificationAward extends EditRecord
{
    protected static string $resource = CertificationAwardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
