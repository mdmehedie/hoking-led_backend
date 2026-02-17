<?php

use Illuminate\Support\Facades\Cache;
use App\Models\AppSetting;

if (! function_exists('setting')) {
    function setting($key)
    {
        return Cache::rememberForever('setting.' . $key, function () use ($key) {
            $setting = AppSetting::first();
            if (! $setting) {
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
        });
    }
}
