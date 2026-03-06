<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedisConfigController;

// Redis Configuration Routes
Route::prefix('admin/redis')->middleware(['auth', 'can:access admin'])->group(function () {
    Route::get('/config', [RedisConfigController::class, 'getConfig'])->name('redis.config');
    Route::post('/test-connection', [RedisConfigController::class, 'testConnection'])->name('redis.test');
    Route::get('/server-info', [RedisConfigController::class, 'getServerInfo'])->name('redis.info');
    Route::post('/clear-cache', [RedisConfigController::class, 'clearCache'])->name('redis.clear-cache');
});
