<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

if (! function_exists('setting')) {
    function setting($key)
    {
        return Cache::rememberForever('setting.' . $key, function () use ($key) {
            return Setting::where('key', $key)->value('value');
        });
    }
}
