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
use App\Http\Controllers\Api\V1\ApiFrontendAppSettingController;
use App\Http\Controllers\Api\V1\ApiFrontendCertificationAwardController;

Route::prefix('v1')->group(function () {
    Route::get('sliders', [ApiFrontendSliderController::class, 'index']);
    Route::get('blogs', [ApiFrontendBlogController::class, 'index']);
    Route::get('blogs/{slug}', [ApiFrontendBlogController::class, 'show']);
    Route::get('case-studies', [ApiFrontendCaseStudyController::class, 'index']);
    Route::get('case-studies/{slug}', [ApiFrontendCaseStudyController::class, 'show']);
    Route::get('products', [ApiFrontendProductController::class, 'index']);
    Route::get('products/{slug}', [ApiFrontendProductController::class, 'show']);
    Route::get('categories', [ApiFrontendCategoryController::class, 'index']);
    Route::get('featured-products', [ApiFrontendFeaturedProductController::class, 'index']);
    Route::get('news', [ApiFrontendNewsController::class, 'index']);
    Route::get('news/{slug}', [ApiFrontendNewsController::class, 'show']);
    Route::get('certifications', [ApiFrontendCertificationAwardController::class, 'index']);
    Route::get('certifications/{slug}', [ApiFrontendCertificationAwardController::class, 'show']);
    Route::get('pages', [ApiFrontendPageController::class, 'index']);
    Route::get('pages/{slug}', [ApiFrontendPageController::class, 'show']);
    Route::get('app-settings', [ApiFrontendAppSettingController::class, 'index']);
    Route::get('app-settings/{column}', [ApiFrontendAppSettingController::class, 'show']);
    // Custom Forms API
    Route::get('forms', [ApiFrontendFormController::class, 'index']);
    Route::post('forms/{form}/submit', [ApiFrontendFormController::class, 'store']);
});
