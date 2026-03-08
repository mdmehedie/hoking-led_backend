<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use App\Filament\Admin\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegion extends EditRecord
{
    protected static string $resource = RegionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If setting as default, remove default from other regions
        if ($data['is_default'] ?? false) {
            \App\Models\Region::where('id', '!=', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
