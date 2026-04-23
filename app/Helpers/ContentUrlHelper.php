<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ContentUrlHelper
{
    /**
     * Convert relative image URLs in HTML content to absolute URLs.
     *
     * @param string $content HTML content with potential relative image paths
     * @param string $disk Storage disk to use (default: 'public')
     * @return string HTML content with absolute image URLs
     */
    public static function convertImageUrlsToAbsolute(string $content, string $disk = 'public'): string
    {
        // Match all img tags with src attribute
        $pattern = '/<img([^>]*)\s+src=["\']([^"\']+)["\']([^>]*)>/i';
        
        return preg_replace_callback($pattern, function ($matches) use ($disk) {
            $before = $matches[1];
            $src = $matches[2];
            $after = $matches[3];
            
            $src = self::convertSingleUrlToAbsolute($src, $disk);
            
            return '<img' . $before . ' src="' . $src . '"' . $after . '>';
        }, $content);
    }

    /**
     * Convert all URLs in HTML content to absolute URLs (links, images, etc).
     *
     * @param string $content HTML content with potential relative URLs
     * @param string $disk Storage disk to use (default: 'public')
     * @return string HTML content with absolute URLs
     */
    public static function convertAllUrlsToAbsolute(string $content, string $disk = 'public'): string
    {
        // Match href and src attributes
        $pattern = '/(href|src)=["\']([^"\']+)["\']/i';

        return preg_replace_callback($pattern, function ($matches) use ($disk) {
            $attribute = $matches[1];
            $url = $matches[2];

            $url = self::convertSingleUrlToAbsolute($url, $disk);

            return $attribute . '="' . $url . '"';
        }, $content);
    }

    /**
     * Check if a URL is relative.
     *
     * @param string $url
     * @return bool
     */
    public static function isRelativeUrl(string $url): bool
    {
        return !preg_match('/^(https?:\/\/|\/\/|#|mailto:|tel:|javascript:|data:)/i', $url);
    }

    /**
     * Convert a single relative URL to absolute.
     *
     * @param string $url Relative URL
     * @param string $disk Storage disk to use (default: 'public')
     * @return string Absolute URL
     */
    public static function convertSingleUrlToAbsolute(string $url, string $disk = 'public'): string
    {
        if (!self::isRelativeUrl($url)) {
            return $url;
        }

        // If it starts with /storage/ or storage/, we need to handle it carefully to avoid double storage prefix
        if (preg_match('#^/?storage/(.*)#i', $url, $matches)) {
            $innerPath = $matches[1];
            // $innerPath is what's AFTER 'storage/'
            return url(Storage::disk($disk)->url($innerPath));
        }

        // Otherwise, it's a direct relative path (e.g., 'images/logo.png')
        $url = ltrim($url, '/');
        return url(Storage::disk($disk)->url($url));
    }
}
