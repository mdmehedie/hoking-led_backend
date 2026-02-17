<?php

namespace App\Filament\Admin\Resources\AppSettingResource\Pages;

use App\Filament\Admin\Resources\AppSettingResource;
use App\Models\AppSetting;
use Filament\Resources\Pages\ListRecords;

class ListAppSettings extends ListRecords
{
    protected static string $resource = AppSettingResource::class;

    public function mount(): void
    {
        parent::mount();

        $setting = AppSetting::query()->first();

        if ($setting) {
            $this->redirect(AppSettingResource::getUrl('edit', ['record' => $setting]));
            return;
        }

        $this->redirect(AppSettingResource::getUrl('create'));
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
