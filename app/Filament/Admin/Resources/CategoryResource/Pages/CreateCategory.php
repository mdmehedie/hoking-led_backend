<?php

namespace App\Filament\Admin\Resources\CategoryResource\Pages;

use App\Filament\Admin\Resources\CategoryResource;
use App\Models\Locale;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('create category');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locales = Locale::activeCodes();

        // Initialize translatable fields for each locale
        foreach ($locales as $locale) {
            $data['name'][$locale] = '';
            $data['description'][$locale] = '';
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
        foreach (['name', 'description'] as $field) {
            if (isset($grouped[$field])) {
                foreach ($locales as $locale) {
                    if (isset($grouped[$field][$locale])) {
                        $this->record->setTranslation($field, $locale, $grouped[$field][$locale]);
                    }
                }
                $data[$field] = $grouped[$field][$defaultLocale] ?? '';
            }
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
}
