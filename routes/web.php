<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\EditorImageUploadController;
use App\Http\Controllers\PWAController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\Admin\AdminLocaleController;
use App\Http\Controllers\AuthDebugController;
use App\Http\Controllers\Frontend\FrontendBlogController;
use App\Http\Controllers\Frontend\FrontendProductController;
use App\Http\Controllers\Frontend\FrontendPageController;
use App\Http\Controllers\Frontend\FrontendCaseStudyController;
use App\Http\Controllers\Frontend\FrontendNewsController;

$supportedLocales = config('app.supported_locales', []);
$localePattern = $supportedLocales !== [] ? implode('|', array_map('preg_quote', $supportedLocales)) : '[a-zA-Z\-]+';

// Get active regions for routing - use try-catch to handle database not ready
$regions = [];
try {
    $regions = \App\Models\Region::activeCodes();
} catch (\Exception $e) {
    // Fallback regions if database is not available
    $regions = ['us', 'uk', 'eu', 'ca', 'au', 'bd'];
}
$regionPattern = !empty($regions) ? implode('|', $regions) : '[a-z]{2}';

// Region-based routes for international SEO (must come before locale routes)
Route::group([
    'prefix' => '{region}',
    'where' => ['region' => $regionPattern],
    'middleware' => ['region.detection'],
], function () {
    // Blog routes
    Route::get('/blog', [FrontendBlogController::class, 'index'])->name('frontend.blog.index.region');
    Route::get('/blog/{slug}', [FrontendBlogController::class, 'show'])->name('frontend.blog.show.region');
    
    // Product routes
    Route::get('/products', [FrontendProductController::class, 'index'])->name('frontend.products.index.region');
    Route::get('/products/{slug}', [FrontendProductController::class, 'show'])->name('frontend.products.show.region');
    
    // Page routes
    Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->name('frontend.page.show.region');
    
    // Case Study routes
    Route::get('/case-studies', [FrontendCaseStudyController::class, 'index'])->name('frontend.case-studies.index.region');
    Route::get('/case-studies/{slug}', [FrontendCaseStudyController::class, 'show'])->name('frontend.case-studies.show.region');
    
    // News routes
    Route::get('/news', [FrontendNewsController::class, 'index'])->name('frontend.news.index.region');
    Route::get('/news/{slug}', [FrontendNewsController::class, 'show'])->name('frontend.news.show.region');
});

Route::group([
    'prefix' => '{locale?}',
    'where' => ['locale' => $localePattern],
], function () {
    // Admin panel is now served directly from root
    // No redirect needed - Filament handles the root route
});

// Default (non-region) routes
Route::get('/blog', [FrontendBlogController::class, 'index'])->name('frontend.blog.index');
Route::get('/blog/{slug}', [FrontendBlogController::class, 'show'])->name('frontend.blog.show');
Route::get('/products', [FrontendProductController::class, 'index'])->name('frontend.products.index');
Route::get('/products/{slug}', [FrontendProductController::class, 'show'])->name('frontend.products.show');
Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->name('frontend.page.show');
Route::get('/case-studies', [FrontendCaseStudyController::class, 'index'])->name('frontend.case-studies.index');
Route::get('/case-studies/{slug}', [FrontendCaseStudyController::class, 'show'])->name('frontend.case-studies.show');
Route::get('/news', [FrontendNewsController::class, 'index'])->name('frontend.news.index');
Route::get('/news/{slug}', [FrontendNewsController::class, 'show'])->name('frontend.news.show');

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
    Route::post('/editor-image-upload', [EditorImageUploadController::class, 'store'])->name('editor.image.upload');

    // Admin locale update
    Route::post('/locale', [AdminLocaleController::class, 'update'])->name('admin.locale.update');

    // Debug routes for production - REMOVE AFTER DEBUGGING
    Route::prefix('debug')->group(function () {
        Route::get('/auth', [AuthDebugController::class, 'checkAuth']);
        Route::get('/env', [AuthDebugController::class, 'checkEnvironment']);
    });
});
