<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only cache static assets
        if ($this->isAsset($request)) {
            $maxAge = $this->getCacheDuration($request);
            
            $response->headers->set('Cache-Control', "public, max-age={$maxAge}, immutable");
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $maxAge));
            
            // Add ETag for better caching
            $etag = md5($response->getContent());
            $response->headers->set('ETag', "\"{$etag}\"");
            
            // Check if client has cached version
            if ($request->headers->get('If-None-Match') === "\"{$etag}\"") {
                return response('', 304);
            }
        }

        return $response;
    }

    /**
     * Determine if the request is for a static asset.
     */
    private function isAsset(Request $request): bool
    {
        $path = $request->path();
        $extensions = [
            'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
            'woff', 'woff2', 'ttf', 'eot', 'mp4', 'webm', 'mp3',
            'pdf', 'zip', 'txt', 'xml', 'json', 'webp', 'avif'
        ];

        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $extensions);
    }

    /**
     * Get cache duration based on asset type.
     */
    private function getCacheDuration(Request $request): int
    {
        $path = $request->path();
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Long cache for versioned assets
        if (preg_match('/\.[0-9a-f]{8,}\./', $path)) {
            return 31536000; // 1 year
        }

        // Different durations for different asset types
        $durations = [
            'css' => 31536000,  // 1 year
            'js' => 31536000,   // 1 year
            'png' => 2592000,   // 30 days
            'jpg' => 2592000,   // 30 days
            'jpeg' => 2592000,  // 30 days
            'gif' => 2592000,   // 30 days
            'svg' => 2592000,   // 30 days
            'ico' => 2592000,   // 30 days
            'woff' => 31536000, // 1 year
            'woff2' => 31536000, // 1 year
            'ttf' => 31536000,  // 1 year
            'eot' => 31536000,  // 1 year
            'webp' => 2592000,  // 30 days
            'avif' => 2592000,  // 30 days
            'mp4' => 2592000,   // 30 days
            'webm' => 2592000,  // 30 days
            'mp3' => 2592000,   // 30 days
            'pdf' => 86400,     // 1 day
            'zip' => 86400,     // 1 day
        ];

        return $durations[$extension] ?? 86400; // Default 1 day
    }
}
