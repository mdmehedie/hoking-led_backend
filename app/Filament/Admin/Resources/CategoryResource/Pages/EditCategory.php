<?php

namespace App\Filament\Admin\Resources\CategoryResource\Pages;

use App\Filament\Admin\Resources\CategoryResource;
use App\Models\Locale;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('edit category');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locales = Locale::activeCodes();

        // Initialize translatable fields as arrays
        foreach (['name', 'description'] as $field) {
            if (!isset($data[$field]) || !is_array($data[$field])) {
                $data[$field] = [];
            }
        }

        // Populate each locale
        foreach ($locales as $locale) {
            $data['name'][$locale] = $this->record->getTranslation('name', $locale, false);
            $data['description'][$locale] = $this->record->getTranslation('description', $locale, false);
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
