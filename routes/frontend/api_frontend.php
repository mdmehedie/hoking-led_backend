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
use App\Http\Controllers\Api\V1\ApiFrontendTestimonialController;
use App\Http\Controllers\Api\V1\ApiFrontendCoreAdvantageController;
use App\Http\Controllers\Api\V1\ApiFrontendLocaleController;
use App\Http\Controllers\Api\V1\ApiFrontendFormController;

// Get active regions for routing
$regions = [];
try {
    $regions = \App\Models\Region::activeCodes();
} catch (\Exception $e) {
    $regions = ['us', 'uk', 'eu', 'ca', 'au', 'bd'];
}
$regionPattern = !empty($regions) ? implode('|', $regions) : '[a-z]{2}';

Route::prefix('v1')->group(function () {
    Route::get('locales', [ApiFrontendLocaleController::class, 'index'])->name('locales.index');
    Route::get('sliders', [ApiFrontendSliderController::class, 'index'])->name('sliders.index');
    
    Route::get('blogs', [ApiFrontendBlogController::class, 'index'])->name('blogs.index');
    Route::get('blogs/{slug}', [ApiFrontendBlogController::class, 'show'])->name('blogs.show');
    
    Route::get('case-studies', [ApiFrontendCaseStudyController::class, 'index'])->name('case-studies.index');
    Route::get('case-studies/{slug}', [ApiFrontendCaseStudyController::class, 'show'])->name('case-studies.show');
    
    Route::get('products', [ApiFrontendProductController::class, 'index'])->name('products.index');
    Route::get('products/{slug}', [ApiFrontendProductController::class, 'show'])->name('products.show');
    
    Route::get('categories', [ApiFrontendCategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{slug}', [ApiFrontendCategoryController::class, 'show'])->name('categories.show');
    
    Route::get('featured-products', [ApiFrontendFeaturedProductController::class, 'index'])->name('featured-products.index');
    
    Route::get('news', [ApiFrontendNewsController::class, 'index'])->name('news.index');
    Route::get('news/{slug}', [ApiFrontendNewsController::class, 'show'])->name('news.show');
    
    Route::get('certifications', [ApiFrontendCertificationAwardController::class, 'index'])->name('certifications.index');
    Route::get('certifications/{slug}', [ApiFrontendCertificationAwardController::class, 'show'])->name('certifications.show');
    
    Route::get('testimonials', [ApiFrontendTestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('core-advantages', [ApiFrontendCoreAdvantageController::class, 'index'])->name('core-advantages.index');
    
    Route::get('pages', [ApiFrontendPageController::class, 'index'])->name('pages.index');
    Route::get('pages/{slug}', [ApiFrontendPageController::class, 'show'])->name('pages.show');
    
    Route::get('app-settings', [ApiFrontendAppSettingController::class, 'index'])->name('app-settings.index');
    Route::get('app-settings/{column}', [ApiFrontendAppSettingController::class, 'show'])->name('app-settings.show');
    
    Route::get('forms', [ApiFrontendFormController::class, 'index'])->name('forms.index');
    Route::post('forms/{form}/submit', [ApiFrontendFormController::class, 'store'])->name('forms.submit');
});

// Region-specific API routes for international SEO
//Route::prefix('v1/{region}')->where(['region' => $regionPattern])->group(function () {
//    Route::get('blogs', [ApiFrontendBlogController::class, 'index']);
//    Route::get('blogs/{slug}', [ApiFrontendBlogController::class, 'show']);
//    Route::get('case-studies', [ApiFrontendCaseStudyController::class, 'index']);
//    Route::get('case-studies/{slug}', [ApiFrontendCaseStudyController::class, 'show']);
//    Route::get('products', [ApiFrontendProductController::class, 'index']);
//    Route::get('products/{slug}', [ApiFrontendProductController::class, 'show']);
//    Route::get('categories', [ApiFrontendCategoryController::class, 'index']);
//    Route::get('categories/{slug}', [ApiFrontendCategoryController::class, 'show']);
//    Route::get('news', [ApiFrontendNewsController::class, 'index']);
//    Route::get('news/{slug}', [ApiFrontendNewsController::class, 'show']);
//    Route::get('pages/{slug}', [ApiFrontendPageController::class, 'show']);
//});
