<?php

namespace App\Observers;

use App\Models\AppSetting;
use App\Services\FrontendRevalidationService;
use Illuminate\Support\Facades\Cache;

class AppSettingObserver
{
    public function saved(AppSetting $appSetting): void
    {
        $this->flush();
    }

    public function deleted(AppSetting $appSetting): void
    {
        $this->flush();
    }

    protected function flush(): void
    {
        Cache::tags(['app_settings'])->flush();

        FrontendRevalidationService::revalidate('app-settings');
    }
}