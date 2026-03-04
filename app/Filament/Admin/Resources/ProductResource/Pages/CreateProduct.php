<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Models\Locale;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $locales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        // Handle title translations
        if (isset($data['title']) && is_array($data['title'])) {
            foreach ($locales as $locale) {
                if (isset($data['title'][$locale])) {
                    $this->record->setTranslation('title', $locale, $data['title'][$locale]);
                }
            }
            // Set default locale value for the main field
            $data['title'] = $data['title'][$defaultLocale] ?? '';
        }

        // Handle short_description translations
        if (isset($data['short_description']) && is_array($data['short_description'])) {
            foreach ($locales as $locale) {
                if (isset($data['short_description'][$locale])) {
                    $this->record->setTranslation('short_description', $locale, $data['short_description'][$locale]);
                }
            }
            // Set default locale value for the main field
            $data['short_description'] = $data['short_description'][$defaultLocale] ?? '';
        }

        // Handle detailed_description translations
        if (isset($data['detailed_description']) && is_array($data['detailed_description'])) {
            foreach ($locales as $locale) {
                if (isset($data['detailed_description'][$locale])) {
                    $this->record->setTranslation('detailed_description', $locale, $data['detailed_description'][$locale]);
                }
            }
            // Set default locale value for the main field
            $data['detailed_description'] = $data['detailed_description'][$defaultLocale] ?? '';
        }

        return $data;
    }
}
