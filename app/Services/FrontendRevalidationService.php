<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FrontendRevalidationService
{
    /**
     * Revalidate the Next.js frontend's cache for the given tag(s) and/or path.
     *
     * @param string|array<int, string> $tags
     */
    public static function revalidate(string|array $tags = [], ?string $path = null): void
    {
        $url = config('services.frontend.url');
        $secret = config('services.frontend.revalidate_secret');

        if (!$url || !$secret) {
            return;
        }

        $tags = is_array($tags) ? $tags : [$tags];

        if (empty($tags) && !$path) {
            return;
        }

        try {
            Http::timeout(5)->post("{$url}/api/revalidate?secret={$secret}", array_filter([
                'tags' => $tags,
                'path' => $path,
            ]));
        } catch (\Throwable $e) {
            Log::warning('Failed to revalidate frontend cache', [
                'tags' => $tags,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }
    }
}