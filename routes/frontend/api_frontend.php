<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiFrontendSliderController;
use App\Http\Controllers\Api\V1\ApiFrontendBlogController;
use App\Http\Controllers\Api\V1\ApiFrontendCaseStudyController;
use App\Http\Controllers\Api\V1\ApiFrontendProductController;
use App\Http\Controllers\Api\V1\ApiFrontendCategoryController;
use App\Http\Controllers\Api\V1\ApiFrontendFeaturedProductController;
use App\Http\Controllers\Api\V1\ApiFrontendNewsController;
use App\Http\Controllers\Api\V1\ApiFrontendPageController;

Route::prefix('v1')->group(function () {
    Route::get('sliders', [ApiFrontendSliderController::class, 'index']);
    Route::get('blogs', [ApiFrontendBlogController::class, 'index']);
    Route::get('case-studies', [ApiFrontendCaseStudyController::class, 'index']);
    Route::get('products', [ApiFrontendProductController::class, 'index']);
    Route::get('categories', [ApiFrontendCategoryController::class, 'index']);
    Route::get('featured-products', [ApiFrontendFeaturedProductController::class, 'index']);
    Route::get('news', [ApiFrontendNewsController::class, 'index']);
    Route::get('pages', [ApiFrontendPageController::class, 'index']);
});
