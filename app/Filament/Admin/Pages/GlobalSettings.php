<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\AppSettingResource;
use App\Models\AppSetting;
use Filament\Pages\Page;

class GlobalSettings extends Page
{
    protected static ?string $slug = 'settings';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.admin.pages.global-settings';

    public function mount(): void
    {
        $setting = AppSetting::query()->first();

        if (! $setting) {
            $setting = AppSetting::query()->create([
                'primary_color' => '#3b82f6',
                'secondary_color' => '#10b981',
                'accent_color' => '#f59e0b',
                'font_family' => 'Arial',
                'base_font_size' => '16px',
            ]);
        }

        $this->redirect(AppSettingResource::getUrl('edit', ['record' => $setting]));
    }
}
