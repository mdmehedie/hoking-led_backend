<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Models\AppSetting;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());

        return $panel
            ->id('admin')
            ->path('admin')
            ->brandName($settings->app_name ?? 'Admin Panel')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                \Filament\Widgets\AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook('head.end', function () {
                $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());
                return $settings && $settings->toastr_enabled ? '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"><script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>' : '';
            })
            ->renderHook('scripts.after', function () {
                $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());
                if (!$settings || !$settings->toastr_enabled) return null;
                return new \Illuminate\Support\HtmlString("<script> toastr.options = { positionClass: 'toast-{$settings->toastr_position}', timeOut: {$settings->toastr_timeout} * 1000, extendedTimeOut: {$settings->toastr_extended_timeout} * 1000, closeButton: " . ($settings->toastr_close_button ? 'true' : 'false') . ", debug: false, newestOnTop: " . ($settings->toastr_newest_on_top ? 'true' : 'false') . ", progressBar: " . ($settings->toastr_progress_bar ? 'true' : 'false') . ", preventDuplicates: " . ($settings->toastr_prevent_duplicates ? 'true' : 'false') . ", showDuration: {$settings->toastr_show_duration}, hideDuration: {$settings->toastr_hide_duration}, showEasing: '{$settings->toastr_show_easing}', hideEasing: '{$settings->toastr_hide_easing}', showMethod: '{$settings->toastr_show_method}', hideMethod: '{$settings->toastr_hide_method}' }; document.addEventListener('toastr', function(e) { if (typeof toastr !== 'undefined') { toastr[e.detail.type](e.detail.message, e.detail.title); } }); </script>");
            });
    }
}
