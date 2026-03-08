<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use App\Filament\Admin\Resources\RegionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegion extends CreateRecord
{
    protected static string $resource = RegionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If setting as default, remove default from other regions
        if ($data['is_default'] ?? false) {
            \App\Models\Region::where('is_default', true)->update(['is_default' => false]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
