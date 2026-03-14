<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

// Analytics API routes
Route::prefix('api/analytics')->group(function () {
    Route::post('/track-event', [AnalyticsController::class, 'trackEvent']);
    Route::get('/stats', [AnalyticsController::class, 'getStats']);
    Route::get('/funnel', [AnalyticsController::class, 'getFunnelData']);
});
