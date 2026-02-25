<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PWAController extends Controller
{
    /**
     * Generate manifest.json dynamically from settings
     */
    public function manifest()
    {
        $settings = Cache::remember('app_settings', 3600, fn () => AppSetting::first());

        if (!$settings) {
            return response()->json([
                'name' => 'App',
                'short_name' => 'App',
                'start_url' => '/',
                'display' => 'standalone',
                'background_color' => '#ffffff',
                'theme_color' => '#000000'
            ]);
        }

        // Check if PWA is enabled
        if (isset($settings->pwa_enabled) && !$settings->pwa_enabled) {
            return response()->json([
                'name' => $settings->app_name ?? 'App',
                'short_name' => $settings->app_name ?? 'App',
                'start_url' => '/',
                'display' => 'browser'
            ]);
        }

        $manifest = [
            'name' => $settings->app_name ?? 'App',
            'short_name' => $settings->pwa_short_name ?? $settings->app_name ?? 'App',
            'description' => $settings->pwa_description ?? $settings->organization['about'] ?? 'Progressive Web App',
            'start_url' => $settings->pwa_start_url ?? '/',
            'display' => $settings->pwa_display_mode ?? 'standalone',
            'background_color' => $settings->pwa_background_color ?? $settings->primary_color ?? '#ffffff',
            'theme_color' => $settings->pwa_theme_color ?? $settings->secondary_color ?? '#000000',
            'orientation' => $settings->pwa_orientation ?? 'portrait-primary',
            'scope' => $settings->pwa_scope ?? '/',
            'lang' => $settings->pwa_lang ?? 'en-US',
            'dir' => $settings->pwa_dir ?? 'ltr'
        ];

        // Add icons if they exist
        $icons = $this->getIcons($settings);
        if (!empty($icons)) {
            $manifest['icons'] = $icons;
        }

        // Add categories if specified
        if ($settings->pwa_categories) {
            $categories = is_array($settings->pwa_categories) ? $settings->pwa_categories : json_decode($settings->pwa_categories, true);
            if ($categories) {
                $manifest['categories'] = $categories;
            }
        } else {
            $manifest['categories'] = ['business', 'productivity'];
        }

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/manifest+json',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    /**
     * Get PWA icons from settings
     */
    private function getIcons($settings)
    {
        $icons = [];

        // Check for PWA icon fields (to be added)
        $pwaIconFields = [
            'pwa_icon_192' => 192,
            'pwa_icon_512' => 512,
            'pwa_icon_72' => 72,
            'pwa_icon_96' => 96,
            'pwa_icon_128' => 128,
            'pwa_icon_144' => 144,
        ];

        foreach ($pwaIconFields as $field => $size) {
            if (isset($settings->$field) && $settings->$field) {
                $iconUrl = Storage::url($settings->$field);

                $icons[] = [
                    'src' => $iconUrl,
                    'sizes' => "{$size}x{$size}",
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ];
            }
        }

        // Fallback to existing favicon/logo if no PWA icons are set
        if (empty($icons)) {
            if ($settings->favicon) {
                $icons[] = [
                    'src' => Storage::url($settings->favicon),
                    'sizes' => '32x32',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ];
            }

            if ($settings->logo_light) {
                $icons[] = [
                    'src' => Storage::url($settings->logo_light),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ];
            }
        }

        return $icons;
    }

    /**
     * Service worker endpoint (placeholder for frontend)
     * This serves as a basic service worker that can be extended by frontend
     */
    public function serviceWorker()
    {
        $serviceWorker = <<<SW
// Basic service worker template
// This should be replaced with a proper service worker implementation

const CACHE_NAME = 'app-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js'
];

// Install event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(urlsToCache))
    );
});

// Fetch event
self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            }
        )
    );
});

// Activate event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
SW;

        return response($serviceWorker, 200, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}
