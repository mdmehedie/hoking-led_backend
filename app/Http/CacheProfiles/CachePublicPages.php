<?php

namespace App\Http\CacheProfiles;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\ResponseCache\CacheProfiles\CacheProfile as BaseCacheProfile;
use Spatie\ResponseCache\Hasher\RequestHasher;

class CachePublicPages extends BaseCacheProfile
{
    /**
     * Determine if the given request should be cached.
     */
    public function shouldCacheRequest(Request $request): bool
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return false;
        }

        // Don't cache if user is authenticated (except for specific public pages)
        if (auth()->check() && !$this->isPublicPageForAuthenticatedUsers($request)) {
            return false;
        }

        // Don't cache if there are flash messages
        if (session()->has('flash')) {
            return false;
        }

        // Don't cache if there are query parameters (except specific allowed ones)
        if ($request->query() && !$this->hasAllowedQueryParameters($request)) {
            return false;
        }

        // Don't cache admin panel
        if ($request->is('admin/*') || $request->is('filament/*')) {
            return false;
        }

        // Don't cache API routes
        if ($request->is('api/*')) {
            return false;
        }

        // Don't cache debug requests
        if ($request->has('debug') || $request->has('xdebug')) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the given response should be cached.
     */
    public function shouldCacheResponse(Response $response): bool
    {
        // Don't cache error responses
        if ($response->getStatusCode() >= 400) {
            return false;
        }

        // Don't cache responses with no-cache header
        if ($response->headers->has('Cache-Control') && 
            str_contains($response->headers->get('Cache-Control'), 'no-cache')) {
            return false;
        }

        return true;
    }

    /**
     * Get the cache time in seconds.
     */
    public function cacheTimeInSeconds(Request $request): int
    {
        // Longer cache for static pages
        if ($this->isStaticPage($request)) {
            return 60 * 60 * 24; // 24 hours
        }

        // Medium cache for dynamic pages
        if ($this->isDynamicPage($request)) {
            return 60 * 60 * 6; // 6 hours
        }

        // Short cache for other pages
        return 60 * 60; // 1 hour
    }

    /**
     * Get a unique key for the request.
     */
    public function cacheName(Request $request): string
    {
        $key = 'response_cache_' . $request->fullUrl();
        
        // Include locale in cache key
        $key .= '_locale_' . app()->getLocale();
        
        // Include user type (guest/authenticated)
        $key .= '_user_' . (auth()->check() ? 'auth' : 'guest');
        
        return $key;
    }

    /**
     * Determine if the request is for a public page that authenticated users can access.
     */
    private function isPublicPageForAuthenticatedUsers(Request $request): bool
    {
        $publicPages = [
            '/',
            '/home',
            '/about',
            '/contact',
            '/products',
            '/services',
            '/blog',
            '/portfolio',
            '/pricing',
        ];

        return in_array($request->path(), $publicPages);
    }

    /**
     * Determine if the request has allowed query parameters.
     */
    private function hasAllowedQueryParameters(Request $request): bool
    {
        $allowedParams = ['page', 'sort', 'filter', 'search', 'category', 'tag'];
        
        $queryKeys = array_keys($request->query());
        
        return empty(array_diff($queryKeys, $allowedParams));
    }

    /**
     * Determine if the request is for a static page.
     */
    private function isStaticPage(Request $request): bool
    {
        $staticPages = [
            '/',
            '/home',
            '/about',
            '/contact',
            '/pricing',
        ];

        return in_array($request->path(), $staticPages);
    }

    /**
     * Determine if the request is for a dynamic page.
     */
    private function isDynamicPage(Request $request): bool
    {
        $dynamicPatterns = [
            'products/*',
            'blog/*',
            'services/*',
            'portfolio/*',
        ];

        foreach ($dynamicPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
