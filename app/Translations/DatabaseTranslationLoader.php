<?php

namespace App\Translations;

use App\Models\UiTranslation;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Arr;

class DatabaseTranslationLoader implements Loader
{
    public function __construct(
        private readonly Loader $fileLoader,
    ) {
    }

    public function load($locale, $group, $namespace = null): array
    {
        $namespace = $namespace ?: '*';

        $fileLines = $this->fileLoader->load($locale, $group, $namespace);

        $dbLines = $this->loadFromDatabase((string) $locale, (string) $group, (string) $namespace);

        return array_replace_recursive($fileLines, $dbLines);
    }

    public function addNamespace($namespace, $hint): void
    {
        $this->fileLoader->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path): void
    {
        $this->fileLoader->addJsonPath($path);
    }

    public function namespaces(): array
    {
        return $this->fileLoader->namespaces();
    }

    private function loadFromDatabase(string $locale, string $group, string $namespace): array
    {
        if ($namespace !== '*' || $group === '') {
            return [];
        }

        if ($group === '*') {
            // JSON translations: __('Some sentence')
            return UiTranslation::query()
                ->where('locale', $locale)
                ->where('key', 'not like', '%.%')
                ->pluck('value', 'key')
                ->toArray();
        }

        // Group translations: __('blog.shared') => group=blog, item=shared
        $prefix = $group . '.';

        $rows = UiTranslation::query()
            ->where('locale', $locale)
            ->where('key', 'like', $prefix . '%')
            ->get(['key', 'value']);

        $lines = [];

        foreach ($rows as $row) {
            $relativeKey = substr($row->key, strlen($prefix));
            if ($relativeKey === false || $relativeKey === '') {
                continue;
            }

            Arr::set($lines, $relativeKey, $row->value);
        }

        return $lines;
    }
}
