<?php

namespace App\Http\Middleware;

use App\Models\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', []);
        $default = Locale::defaultCode();

        $locale = $this->determineLocale($request, $supported, $default);

        app()->setLocale($locale);

        return $next($request);
    }

    private function determineLocale(Request $request, array $supported, string $default): string
    {
        $lang = $request->query('lang');
        if (is_string($lang)) {
            $lang = $this->normalize($lang);
            if ($this->isSupported($lang, $supported)) {
                return $lang;
            }
        }

        $routeLocale = $request->route('locale');
        if (is_string($routeLocale)) {
            $routeLocale = $this->normalize($routeLocale);
            if ($this->isSupported($routeLocale, $supported)) {
                return $routeLocale;
            }
        }

        $sessionLocale = $request->session()->get('locale');
        if (is_string($sessionLocale)) {
            $sessionLocale = $this->normalize($sessionLocale);
            if ($this->isSupported($sessionLocale, $supported)) {
                return $sessionLocale;
            }
        }

        $acceptLanguage = $request->header('Accept-Language');
        if (is_string($acceptLanguage) && $acceptLanguage !== '') {
            foreach ($this->parseAcceptLanguage($acceptLanguage) as $candidate) {
                if ($this->isSupported($candidate, $supported)) {
                    return $candidate;
                }
            }
        }

        return $default;
    }

    private function parseAcceptLanguage(string $header): array
    {
        $parts = explode(',', $header);
        $locales = [];

        foreach ($parts as $part) {
            $localePart = trim(explode(';', $part)[0] ?? '');
            if ($localePart === '') {
                continue;
            }

            $locales[] = $this->normalize($localePart);
        }

        return array_values(array_unique($locales));
    }

    private function normalize(string $locale): string
    {
        $locale = strtolower(str_replace('_', '-', $locale));
        $primary = explode('-', $locale)[0] ?? $locale;

        return $primary;
    }

    private function isSupported(string $locale, array $supported): bool
    {
        if ($supported === []) {
            return true;
        }

        return in_array($locale, $supported, true);
    }
}
