<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiFrontendSliderController;

Route::prefix('v1')->group(function () {
    Route::get('sliders', [ApiFrontendSliderController::class, 'index']);
});
