<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EditorImageUploadController;
use App\Http\Controllers\Admin\AdminLocaleController;
use App\Http\Controllers\Api\V1\MediaLibraryController;

Route::middleware('auth')->group(function () {
    Route::post('/editor-image-upload', [EditorImageUploadController::class, 'store'])->name('editor.image.upload');

    Route::prefix('media-library')->group(function () {
        Route::get('/', [MediaLibraryController::class, 'index'])->name('media.library.index');
        Route::post('/upload', [MediaLibraryController::class, 'upload'])->name('media.library.upload');
        Route::delete('/{media}', [MediaLibraryController::class, 'destroy'])->name('media.library.destroy');
    });

    Route::post('/locale', [AdminLocaleController::class, 'update'])->name('admin.locale.update');
});
