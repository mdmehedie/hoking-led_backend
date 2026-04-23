<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiFrontendSliderController;
use App\Http\Controllers\Api\V1\ApiFrontendBlogController;
use App\Http\Controllers\Api\V1\ApiFrontendCaseStudyController;
use App\Http\Controllers\Api\V1\ApiFrontendProductController;
use App\Http\Controllers\Api\V1\ApiFrontendCategoryController;
use App\Http\Controllers\Api\V1\ApiFrontendNewsController;
use App\Http\Controllers\Api\V1\ApiFrontendPageController;
use App\Http\Controllers\Api\V1\ApiFrontendAppSettingController;
use App\Http\Controllers\Api\V1\ApiFrontendCertificationAwardController;
use App\Http\Controllers\Api\V1\ApiFrontendTestimonialController;
use App\Http\Controllers\Api\V1\ApiFrontendCoreAdvantageController;
use App\Http\Controllers\Api\V1\ApiFrontendProjectController;
use App\Http\Controllers\Api\V1\ApiFrontendBrandController;
use App\Http\Controllers\Api\V1\ApiFrontendContactController;
use App\Http\Controllers\Api\V1\ApiFrontendNewsletterController;
use App\Http\Controllers\Api\V1\ApiFrontendTeamMemberController;
use App\Http\Controllers\Api\V1\ApiFrontendLocaleController;
use App\Http\Controllers\Api\V1\ApiFrontendConversationController;
use App\Http\Controllers\Api\V1\ApiFrontendFormController;
use App\Http\Controllers\Api\V1\ApiFrontendVideoController;

Route::prefix('v1')->group(function () {
    Route::get('languages', [ApiFrontendLocaleController::class, 'index'])->name('locales.index');
    Route::get('sliders', [ApiFrontendSliderController::class, 'index'])->name('sliders.index');

    Route::get('videos', [ApiFrontendVideoController::class, 'index'])->name('videos.index');
    Route::get('videos/{slug}', [ApiFrontendVideoController::class, 'show'])->name('videos.show');

    Route::get('blogs', [ApiFrontendBlogController::class, 'index'])->name('blogs.index');
    Route::get('blogs/{slug}', [ApiFrontendBlogController::class, 'show'])->name('blogs.show');

    Route::get('case-studies', [ApiFrontendCaseStudyController::class, 'index'])->name('case-studies.index');
    Route::get('case-studies/{slug}', [ApiFrontendCaseStudyController::class, 'show'])->name('case-studies.show');

    Route::get('products', [ApiFrontendProductController::class, 'index'])->name('products.index');
    Route::get('products/{slug}', [ApiFrontendProductController::class, 'show'])->name('products.show');

    Route::get('categories', [ApiFrontendCategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{slug}', [ApiFrontendCategoryController::class, 'show'])->name('categories.show');

    Route::get('news', [ApiFrontendNewsController::class, 'index'])->name('news.index');
    Route::get('news/{slug}', [ApiFrontendNewsController::class, 'show'])->name('news.show');

    Route::get('certifications', [ApiFrontendCertificationAwardController::class, 'index'])->name('certifications.index');
    Route::get('certifications/{slug}', [ApiFrontendCertificationAwardController::class, 'show'])->name('certifications.show');

    Route::get('testimonials', [ApiFrontendTestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('core-advantages', [ApiFrontendCoreAdvantageController::class, 'index'])->name('core-advantages.index');

    Route::get('projects', [ApiFrontendProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{slug}', [ApiFrontendProjectController::class, 'show'])->name('projects.show');

    Route::get('brands', [ApiFrontendBrandController::class, 'index'])->name('brands.index');
    Route::get('brands/{slug}', [ApiFrontendBrandController::class, 'show'])->name('brands.show');

    Route::post('newsletter/subscribe', [ApiFrontendNewsletterController::class, 'subscribe'])->middleware('throttle:3,1')->name('newsletter.subscribe');
    Route::get('newsletter/unsubscribe/{token}', [ApiFrontendNewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
    Route::get('newsletter/confirm/{token}', [ApiFrontendNewsletterController::class, 'confirm'])->name('newsletter.confirm');

    Route::post('contact/submit', [ApiFrontendContactController::class, 'submit'])->middleware('throttle:10,1')->name('contact.submit');

    Route::post('conversations', [ApiFrontendConversationController::class, 'start'])->middleware('throttle:5,1')->name('conversations.start');
    Route::get('conversations/{sessionId}/messages', [ApiFrontendConversationController::class, 'messages'])->name('conversations.messages');
    Route::post('conversations/{sessionId}/messages', [ApiFrontendConversationController::class, 'sendMessage'])->middleware('throttle:20,1')->name('conversations.send');

    Route::get('team-members', [ApiFrontendTeamMemberController::class, 'index'])->name('team-members.index');
    Route::get('team-members/{slug}', [ApiFrontendTeamMemberController::class, 'show'])->name('team-members.show');

    Route::get('pages', [ApiFrontendPageController::class, 'index'])->name('pages.index');
    Route::get('pages/{slug}', [ApiFrontendPageController::class, 'show'])->name('pages.show');

    Route::get('app-settings', [ApiFrontendAppSettingController::class, 'index'])->name('app-settings.index');
    Route::get('app-settings/{column}', [ApiFrontendAppSettingController::class, 'show'])->name('app-settings.show');

    Route::get('forms', [ApiFrontendFormController::class, 'index'])->name('forms.index');
    Route::post('forms/{formId}/submit', [ApiFrontendFormController::class, 'store'])->name('forms.submit');
});
