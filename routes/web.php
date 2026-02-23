<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\EditorImageUploadController;

Route::get('/', function () {
    return view('welcome');
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

Route::middleware('auth')->group(function () {
    Route::post('/admin/editor-image-upload', [EditorImageUploadController::class, 'store'])->name('editor.image.upload');
});
