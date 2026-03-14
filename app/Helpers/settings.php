<?php

use Illuminate\Support\Facades\Cache;
use App\Models\AppSetting;

if (! function_exists('setting')) {
    function setting($key)
    {
        $setting = Cache::tags(['app_settings'])->rememberForever('app_settings', function () {
            return AppSetting::first();
        });

        if (!$setting) {
            return null;
        }

        $segments = explode('.', (string) $key);
        $value = $setting;

        foreach ($segments as $segment) {
            if (is_array($value)) {
                $value = $value[$segment] ?? null;
                continue;
            }

            if (is_object($value)) {
                $value = $value->{$segment} ?? null;
                continue;
            }

            return null;
        }

        return $value;
    }
}
