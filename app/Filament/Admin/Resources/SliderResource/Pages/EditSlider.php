<?php

namespace App\Filament\Admin\Resources\SliderResource\Pages;

use App\Filament\Admin\Resources\SliderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSlider extends EditRecord
{
    protected static string $resource = SliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit ' . strip_tags($this->record->title);
    }

    public function getBreadcrumbs(): array
    {
        return [
            SliderResource::getUrl() => 'Sliders',
            SliderResource::getUrl('edit', ['record' => $this->record]) => 'Edit ' . strip_tags($this->record->title),
        ];
    }
}
