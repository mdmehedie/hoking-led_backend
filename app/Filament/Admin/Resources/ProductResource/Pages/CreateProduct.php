<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Models\Locale;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locales = Locale::activeCodes();

        // Initialize translatable fields for each locale
        foreach ($locales as $locale) {
            $data['title'][$locale] = '';
            $data['short_description'][$locale] = '';
            $data['detailed_description'][$locale] = [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $locales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        // Group dotted notation fields
        $grouped = static::groupTranslatableFields($data);

        // Save translatable fields
        foreach (['title', 'short_description', 'detailed_description'] as $field) {
            if (isset($grouped[$field])) {
                foreach ($locales as $locale) {
                    if (isset($grouped[$field][$locale])) {
                        $this->record->setTranslation($field, $locale, $grouped[$field][$locale]);
                    }
                }
                $data[$field] = $grouped[$field][$defaultLocale] ?? '';
            }
        }

        // Handle features
        if (isset($grouped['features'])) {
            foreach ($locales as $locale) {
                if (isset($grouped['features'][$locale])) {
                    $features = static::flattenFeatures($grouped['features'][$locale]);
                    $this->record->setTranslation('features', $locale, $features);
                }
            }
            $data['features'] = $grouped['features'][$defaultLocale] ?? [];
        }

        return $data;
    }

    /**
     * Group dotted notation fields into nested arrays.
     */
    private static function groupTranslatableFields(array $data): array
    {
        $grouped = [];
        
        foreach ($data as $key => $value) {
            if (strpos($key, '.') !== false) {
                [$field, $locale] = explode('.', $key, 2);
                $grouped[$field][$locale] = $value;
            }
        }
        
        return $grouped;
    }

    /**
     * Convert repeater format to simple array.
     */
    private static function flattenFeatures(array $features): array
    {
        return collect($features)
            ->filter(fn($item) => !empty($item['feature']))
            ->pluck('feature')
            ->values()
            ->all();
    }
}
