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
            
            // Convert relative URLs to absolute
            if (!preg_match('/^(https?:\/\/|\/\/|data:)/i', $src)) {
                // If path starts with '/storage/' or 'storage/', just make it absolute
                if (preg_match('#^/?storage/#i', $src)) {
                    $src = url($src);
                } else {
                    // Otherwise use Storage disk to generate URL
                    $src = ltrim($src, '/');
                    $src = url(Storage::disk($disk)->url($src));
                }
            }
            
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

            // Convert relative URLs to absolute
            if (!preg_match('/^(https?:\/\/|\/\/|#|mailto:|tel:|javascript:|data:)/i', $url)) {
                $url = ltrim($url, '/');
                $url = url(Storage::disk($disk)->url($url));
            }

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
        if (self::isRelativeUrl($url)) {
            $url = ltrim($url, '/');
            return url(Storage::disk($disk)->url($url));
        }

        return $url;
    }
}
