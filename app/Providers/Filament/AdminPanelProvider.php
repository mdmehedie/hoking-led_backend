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
use App\Filament\Admin\Widgets\PageViewsWidget;
use App\Filament\Admin\Widgets\TopVisitedPagesWidget;
use App\Filament\Admin\Widgets\TrafficSourcesWidget;
use App\Filament\Admin\Widgets\SEODashboardWidget;
use App\Filament\Admin\Widgets\KeywordRankingWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use App\Models\Locale;
use App\Http\Middleware\SetLocale;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = null;
        try {
            $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());
        } catch (\Exception $e) {
            // Table doesn't exist yet, use null
        }

        return $panel
            ->id('admin')
            ->path('')
            ->brandName(function() {
                try {
                    $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());
                    return $settings ? __($settings->app_name ?? 'Admin Panel') : __('Admin Panel');
                } catch (\Exception $e) {
                    return __('Admin Panel');
                }
            })
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make(fn() => __('Content Management')),
                \Filament\Navigation\NavigationGroup::make(fn() => __('Product Management')),
                \Filament\Navigation\NavigationGroup::make(fn() => __('Marketing')),
                \Filament\Navigation\NavigationGroup::make(fn() => __('Settings')),
                \Filament\Navigation\NavigationGroup::make(fn() => __('User Management')),
            ])
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                \Filament\Widgets\AccountWidget::class,
                PageViewsWidget::class,
                SEODashboardWidget::class,
                TopVisitedPagesWidget::class,
                TrafficSourcesWidget::class,
                KeywordRankingWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                // Production debug middleware - REMOVE AFTER DEBUGGING
                \App\Http\Middleware\ProductionDebugMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook('head.end', function () {
                try {
                    $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());
                    return ($settings && $settings->toastr_enabled ? '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"><script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>' : '') .
                        '<style>
                            aside.fi-sidebar.fi-main-sidebar.fi-sidebar-open {
                                box-shadow: 1px 0 4px rgba(0, 0, 0, 0.1) !important;
                            }
                            /* User avatar dropdown arrow - more aggressive approach */
                            .fi-topbar .fi-user-menu .fi-user-menu-trigger {
                                position: relative !important;
                                padding-right: 24px !important;
                            }
                            .fi-topbar .fi-user-menu .fi-user-menu-trigger::after {
                                content: "▼" !important;
                                position: absolute !important;
                                right: 8px !important;
                                top: 50% !important;
                                transform: translateY(-50%) !important;
                                font-size: 10px !important;
                                color: currentColor !important;
                                transition: transform 0.2s ease !important;
                                line-height: 1 !important;
                            }
                            .fi-topbar .fi-user-menu[aria-expanded="true"] .fi-user-menu-trigger::after {
                                content: "▲" !important;
                                transform: translateY(-50%) rotate(0deg) !important;
                            }
                            /* Alternative targeting */
                            div[x-data="filamentDropdown"] .fi-user-menu-trigger::after {
                                content: "▼" !important;
                                position: absolute !important;
                                right: 8px !important;
                                top: 50% !important;
                                transform: translateY(-50%) !important;
                                font-size: 10px !important;
                                color: currentColor !important;
                                transition: transform 0.2s ease !important;
                                line-height: 1 !important;
                            }
                            div[x-data="filamentDropdown"][aria-expanded="true"] .fi-user-menu-trigger::after {
                                content: "▲" !important;
                                transform: translateY(-50%) rotate(0deg) !important;
                            }
                        </style>';
                } catch (\Exception $e) {
                    return '<style>
                        aside.fi-sidebar.fi-main-sidebar.fi-sidebar-open {
                            box-shadow: 1px 0 4px rgba(0, 0, 0, 0.1) !important;
                        }
                        /* User avatar dropdown arrow - more aggressive approach */
                        .fi-topbar .fi-user-menu .fi-user-menu-trigger {
                            position: relative !important;
                            padding-right: 24px !important;
                        }
                        .fi-topbar .fi-user-menu .fi-user-menu-trigger::after {
                            content: "▼" !important;
                            position: absolute !important;
                            right: 8px !important;
                            top: 50% !important;
                            transform: translateY(-50%) !important;
                            font-size: 10px !important;
                            color: currentColor !important;
                            transition: transform 0.2s ease !important;
                            line-height: 1 !important;
                        }
                        .fi-topbar .fi-user-menu[aria-expanded="true"] .fi-user-menu-trigger::after {
                            content: "▲" !important;
                            transform: translateY(-50%) rotate(0deg) !important;
                        }
                        /* Alternative targeting */
                        div[x-data="filamentDropdown"] .fi-user-menu-trigger::after {
                            content: "▼" !important;
                            position: absolute !important;
                            right: 8px !important;
                            top: 50% !important;
                            transform: translateY(-50%) !important;
                            font-size: 10px !important;
                            color: currentColor !important;
                            transition: transform 0.2s ease !important;
                            line-height: 1 !important;
                        }
                        div[x-data="filamentDropdown"][aria-expanded="true"] .fi-user-menu-trigger::after {
                            content: "▲" !important;
                            transform: translateY(-50%) rotate(0deg) !important;
                        }
                    </style>';
                }
            })
            ->renderHook('panels::topbar.end', function () {
                $locales = Locale::query()
                    ->where('is_active', true)
                    ->orderByDesc('is_default')
                    ->orderBy('code')
                    ->get(['code', 'name']);

                return new HtmlString('
                    <style>
                        .locale-switcher-wrapper {
                            position: absolute;
                            right: 360px;
                            top: 50%;
                            transform: translateY(-50%);
                            z-index: 10;
                        }
                        @media (max-width: 768px) {
                            .locale-switcher-wrapper {
                                right: 200px;
                            }
                        }
                        /* Custom user menu arrow button */
                        .custom-user-arrow-btn {
                            background: none;
                            border: none;
                            color: inherit;
                            font-size: 14px;
                            cursor: pointer;
                            padding: 8px;
                            border-radius: 4px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            margin-left: 4px;
                            transition: background-color 0.2s ease;
                        }
                        .custom-user-arrow-btn:hover {
                            background-color: rgba(0, 0, 0, 0.1);
                        }
                        .custom-user-arrow-btn:focus {
                            outline: 2px solid rgba(59, 130, 246, 0.5);
                            outline-offset: 2px;
                        }
                        .fi-topbar .fi-user-menu {
                            display: flex;
                            align-items: center;
                        }
                        .fi-topbar .fi-user-menu .fi-user-menu-trigger {
                            margin-right: 0;
                        }
                    </style>
                    <div class="locale-switcher-wrapper">' .
                    view('filament.components.locale-switcher', ['locales' => $locales])->render() .
                    '</div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const userMenu = document.querySelector(".fi-user-menu");
                            if (userMenu) {
                                const trigger = userMenu.querySelector(".fi-user-menu-trigger");
                                if (trigger) {
                                    // Create arrow button
                                    const arrowBtn = document.createElement("button");
                                    arrowBtn.innerHTML = "&#x25BC;"; // Down arrow
                                    arrowBtn.className = "custom-user-arrow-btn";
                                    arrowBtn.setAttribute("aria-label", "User menu");
                                    arrowBtn.setAttribute("type", "button");

                                    // Insert after the avatar button
                                    trigger.parentNode.insertBefore(arrowBtn, trigger.nextSibling);

                                    // Handle click on arrow button
                                    arrowBtn.addEventListener("click", function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        trigger.click();
                                    });

                                    // Watch for dropdown state changes to update arrow direction
                                    const dropdown = userMenu.querySelector("[x-data]");
                                    if (dropdown) {
                                        const observer = new MutationObserver(function(mutations) {
                                            mutations.forEach(function(mutation) {
                                                if (mutation.attributeName === "aria-expanded") {
                                                    const isExpanded = dropdown.getAttribute("aria-expanded") === "true";
                                                    arrowBtn.innerHTML = isExpanded ? "&#x25B2;" : "&#x25BC;"; // Up or down arrow
                                                }
                                            });
                                        });

                                        observer.observe(dropdown, {
                                            attributes: true,
                                            attributeFilter: ["aria-expanded"]
                                        });
                                    }

                                    // Also make the avatar itself clickable
                                    trigger.addEventListener("click", function(e) {
                                        // Update arrow direction when avatar is clicked
                                        setTimeout(function() {
                                            const dropdown = userMenu.querySelector("[x-data]");
                                            if (dropdown) {
                                                const isExpanded = dropdown.getAttribute("aria-expanded") === "true";
                                                arrowBtn.innerHTML = isExpanded ? "&#x25B2;" : "&#x25BC;";
                                            }
                                        }, 10);
                                    });
                                }
                            }
                        });
                    </script>
                ');
            })
            ->renderHook('scripts.after', function () {
                try {
                    $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());
                    if (!$settings || !$settings->toastr_enabled) return null;
                    return new \Illuminate\Support\HtmlString("<script> toastr.options = { positionClass: 'toast-{$settings->toastr_position}', timeOut: {$settings->toastr_timeout} * 1000, extendedTimeOut: {$settings->toastr_extended_timeout} * 1000, closeButton: " . ($settings->toastr_close_button ? 'true' : 'false') . ", debug: false, newestOnTop: " . ($settings->toastr_newest_on_top ? 'true' : 'false') . ", progressBar: " . ($settings->toastr_progress_bar ? 'true' : 'false') . ", preventDuplicates: " . ($settings->toastr_prevent_duplicates ? 'true' : 'false') . ", showDuration: {$settings->toastr_show_duration}, hideDuration: {$settings->toastr_hide_duration}, showEasing: '{$settings->toastr_show_easing}', hideEasing: '{$settings->toastr_hide_easing}', showMethod: '{$settings->toastr_show_method}', hideMethod: '{$settings->toastr_hide_method}' }; document.addEventListener('toastr', function(e) { if (typeof toastr !== 'undefined') { toastr[e.detail.type](e.detail.message, e.detail.title); } }); </script>");
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->renderHook('scripts.after', function () {
                return new \Illuminate\Support\HtmlString('
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const userMenuTrigger = document.querySelector(".fi-user-menu-trigger");
                            if (userMenuTrigger) {
                                // Create arrow element
                                const arrow = document.createElement("span");
                                arrow.textContent = "▼";
                                arrow.style.cssText = "position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-size: 10px; color: currentColor; transition: transform 0.2s ease; line-height: 1; pointer-events: none;";
                                arrow.className = "user-menu-arrow";

                                // Style the button
                                userMenuTrigger.style.position = "relative";
                                userMenuTrigger.style.paddingRight = "24px";
                                userMenuTrigger.appendChild(arrow);

                                // Listen for dropdown state changes
                                const dropdown = userMenuTrigger.closest("[x-data]");
                                if (dropdown) {
                                    // Watch for aria-expanded changes
                                    const observer = new MutationObserver(function(mutations) {
                                        mutations.forEach(function(mutation) {
                                            if (mutation.attributeName === "aria-expanded") {
                                                const isExpanded = dropdown.getAttribute("aria-expanded") === "true";
                                                arrow.textContent = isExpanded ? "▲" : "▼";
                                                arrow.style.transform = isExpanded ? "translateY(-50%) rotate(0deg)" : "translateY(-50%) rotate(0deg)";
                                            }
                                        });
                                    });

                                    observer.observe(dropdown, {
                                        attributes: true,
                                        attributeFilter: ["aria-expanded"]
                                    });

                                    // Also watch for x-show changes (Alpine.js)
                                    const panel = dropdown.querySelector(".fi-dropdown-panel");
                                    if (panel) {
                                        const panelObserver = new MutationObserver(function(mutations) {
                                            mutations.forEach(function(mutation) {
                                                if (mutation.attributeName === "style") {
                                                    const isVisible = panel.style.display !== "none";
                                                    arrow.textContent = isVisible ? "▲" : "▼";
                                                }
                                            });
                                        });

                                        panelObserver.observe(panel, {
                                            attributes: true,
                                            attributeFilter: ["style"]
                                        });
                                    }
                                }
                            }
                        });
                    </script>
                ');
            })
            ->renderHook('scripts.after', function () {
                try {
                    $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());
                    if (!$settings || !$settings->toastr_enabled) return null;
                    return new \Illuminate\Support\HtmlString("<script> toastr.options = { positionClass: 'toast-{$settings->toastr_position}', timeOut: {$settings->toastr_timeout} * 1000, extendedTimeOut: {$settings->toastr_extended_timeout} * 1000, closeButton: " . ($settings->toastr_close_button ? 'true' : 'false') . ", debug: false, newestOnTop: " . ($settings->toastr_newest_on_top ? 'true' : 'false') . ", progressBar: " . ($settings->toastr_progress_bar ? 'true' : 'false') . ", preventDuplicates: " . ($settings->toastr_prevent_duplicates ? 'true' : 'false') . ", showDuration: {$settings->toastr_show_duration}, hideDuration: {$settings->toastr_hide_duration}, showEasing: '{$settings->toastr_show_easing}', hideEasing: '{$settings->toastr_hide_easing}', showMethod: '{$settings->toastr_show_method}', hideMethod: '{$settings->toastr_hide_method}' }; document.addEventListener('toastr', function(e) { if (typeof toastr !== 'undefined') { toastr[e.detail.type](e.detail.message, e.detail.title); } }); </script>");
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->renderHook('scripts.after', function () {
                return new \Illuminate\Support\HtmlString('
                    <script>
                        document.addEventListener("trix-attachment-add", function(event) {
                            var attachment = event.attachment;
                            if (attachment.file) {
                                uploadFile(attachment);
                            }
                        });

                        function uploadFile(attachment) {
                            var file = attachment.file;
                            var form = new FormData();
                            form.append("file", file);
                            var csrfToken = document.querySelector(\'meta[name="csrf-token"]\');
                            if (csrfToken) {
                                form.append("_token", csrfToken.getAttribute("content"));
                            }
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "/editor-image-upload");
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    var data = JSON.parse(xhr.responseText);
                                    attachment.setAttributes({
                                        url: data.url,
                                        href: data.url
                                    });
                                } else {
                                    console.error("Upload failed");
                                    attachment.remove();
                                }
                            };
                            xhr.onerror = function() {
                                console.error("Upload error");
                                attachment.remove();
                            };
                            xhr.send(form);
                        }
                    </script>
                ');
            });
    }
}
