<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\UiTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationsSync extends Command
{
    protected $signature = 'translations:sync
        {--path= : Path to scan (defaults to app/, resources/views/, routes/)}
        {--fill-en : Fill English (en) value automatically when missing (recommended)}
        {--dry-run : Do not write to DB, only show what would be inserted}';

    protected $description = 'Scan codebase for translation keys (__, @lang) and insert missing ui_translations rows for active locales';

    public function handle(): int
    {
        $scanPath = $this->option('path');

        $paths = [];
        if (is_string($scanPath) && $scanPath !== '') {
            $paths[] = base_path($scanPath);
        } else {
            $paths = [
                app_path(),
                resource_path('views'),
                base_path('routes'),
            ];
        }

        $activeLocales = Locale::activeCodes();
        if ($activeLocales === []) {
            $activeLocales = (array) config('app.supported_locales', ['en']);
        }

        $keys = $this->extractKeys($paths);
        if ($keys === []) {
            $this->info('No translation keys found.');
            return Command::SUCCESS;
        }

        $this->line('Found ' . count($keys) . ' unique key(s).');

        $dryRun = (bool) $this->option('dry-run');
        $fillEn = (bool) $this->option('fill-en');

        $inserted = 0;
        $skipped = 0;

        foreach ($keys as $key) {
            foreach ($activeLocales as $locale) {
                $exists = UiTranslation::query()
                    ->where('key', $key)
                    ->where('locale', $locale)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $value = null;
                if ($fillEn && $locale === 'en') {
                    $value = $this->defaultEnglishValueForKey($key);
                }

                if ($dryRun) {
                    $this->line('[DRY] insert: ' . $locale . ' => ' . $key);
                    $inserted++;
                    continue;
                }

                UiTranslation::query()->create([
                    'key' => $key,
                    'locale' => $locale,
                    'value' => $value,
                ]);

                $inserted++;
            }
        }

        $this->info('Done. Inserted: ' . $inserted . ', already existed: ' . $skipped . '.');

        return Command::SUCCESS;
    }

    private function defaultEnglishValueForKey(string $key): ?string
    {
        // If key looks like a sentence (legacy JSON-style), use it as English value.
        // For dotted keys (module.key), keep null so you can manage values intentionally.
        if (Str::contains($key, '.') && !Str::contains($key, ' ')) {
            return null;
        }

        return $key;
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    private function extractKeys(array $paths): array
    {
        $files = [];

        foreach ($paths as $path) {
            if (!is_string($path) || $path === '' || !File::exists($path)) {
                continue;
            }

            if (File::isFile($path)) {
                $files[] = $path;
                continue;
            }

            $files = array_merge($files, File::allFiles($path));
        }

        $keys = [];

        foreach ($files as $file) {
            $filePath = (string) $file;

            if (!preg_match('/\.(php|blade\.php)$/i', $filePath)) {
                continue;
            }

            $content = File::get($filePath);

            foreach ($this->extractKeysFromContent($content) as $k) {
                $keys[$k] = true;
            }
        }

        $unique = array_keys($keys);
        sort($unique);

        return $unique;
    }

    /**
     * @return array<int, string>
     */
    private function extractKeysFromContent(string $content): array
    {
        $keys = [];

        // __('...') / __("...")
        // @lang('...') / @lang("...")
        // Only extracts literal string keys.
        $patterns = [
            '/__\(\s*[\"\']([^\"\']+)[\"\']\s*[\),]/',
            '/@lang\(\s*[\"\']([^\"\']+)[\"\']\s*[\),]/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $match = trim((string) $match);
                    if ($match === '') {
                        continue;
                    }
                    $keys[] = $match;
                }
            }
        }

        return $keys;
    }
}
