<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\EditorImageUploadController;
use App\Http\Controllers\PWAController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\Admin\AdminLocaleController;
use App\Http\Controllers\AuthDebugController;

$supportedLocales = config('app.supported_locales', []);
$localePattern = $supportedLocales !== [] ? implode('|', array_map('preg_quote', $supportedLocales)) : '[a-zA-Z\-]+';

Route::group([
    'prefix' => '{locale?}',
    'where' => ['locale' => $localePattern],
], function () {
    Route::get('/', function () {
        return redirect('/admin');
    });
});

Route::get('/test', function () {
    phpinfo();
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return 'All caches cleared!';
});

// Robots.txt route
Route::get('/robots.txt', [RobotsTxtController::class, 'index']);

// PWA routes
Route::get('/manifest.json', [PWAController::class, 'manifest']);
Route::get('/sw.js', [PWAController::class, 'serviceWorker']);

Route::middleware('auth')->group(function () {
    Route::post('/admin/editor-image-upload', [EditorImageUploadController::class, 'store'])->name('editor.image.upload');

    // Admin locale update
    Route::post('/admin/locale', [AdminLocaleController::class, 'update'])->name('admin.locale.update');

    // Debug routes for production - REMOVE AFTER DEBUGGING
    Route::prefix('debug')->group(function () {
        Route::get('/auth', [AuthDebugController::class, 'checkAuth']);
        Route::get('/env', [AuthDebugController::class, 'checkEnvironment']);
    });
});
