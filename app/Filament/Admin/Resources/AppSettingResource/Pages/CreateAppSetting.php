<?php

namespace App\Filament\Admin\Resources\AppSettingResource\Pages;

use App\Filament\Admin\Resources\AppSettingResource;
use App\Models\AppSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateAppSetting extends CreateRecord
{
    protected static string $resource = AppSettingResource::class;

    public function mount(): void
    {
        parent::mount();

        $setting = AppSetting::query()->first();

        if ($setting) {
            $this->redirect(AppSettingResource::getUrl('edit', ['record' => $setting]));
        }
    }
}
