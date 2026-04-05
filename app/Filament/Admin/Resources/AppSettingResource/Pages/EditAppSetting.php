<?php

namespace App\Filament\Admin\Resources\AppSettingResource\Pages;

use App\Filament\Admin\Resources\AppSettingResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditAppSetting extends EditRecord
{
    protected static string $resource = AppSettingResource::class;

    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return static::getModel()::with('translations')->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function afterSave(): void
    {
        Cache::forget('app_settings');

        // Clear Redis configuration cache when Redis settings are changed
        $redisFields = [
            'redis_host', 'redis_port', 'redis_password', 'redis_db',
            'redis_cache_db', 'redis_session_db', 'redis_queue_db',
            'redis_prefix', 'redis_cache_enabled', 'redis_session_enabled',
            'redis_queue_enabled', 'redis_cache_ttl', 'redis_session_ttl',
            'redis_client'
        ];

        $changed = false;
        foreach ($redisFields as $field) {
            if ($this->record->wasChanged($field)) {
                $changed = true;
                break;
            }
        }

        if ($changed) {
            \App\Services\RedisConfigService::clearConfigCache();

            // Show notification to user that Redis config has been updated
            \Filament\Notifications\Notification::make()
                ->title('Redis Configuration Updated')
                ->body('Redis settings have been updated. You may need to restart queue workers for changes to take effect.')
                ->success()
                ->send();
        }
    }
}
