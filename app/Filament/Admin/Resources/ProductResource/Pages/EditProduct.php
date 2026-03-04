<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Models\Locale;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locales = Locale::activeCodes();

        if (!isset($data['title']) || !is_array($data['title'])) {
            $data['title'] = [];
        }

        if (!isset($data['short_description']) || !is_array($data['short_description'])) {
            $data['short_description'] = [];
        }

        if (!isset($data['detailed_description']) || !is_array($data['detailed_description'])) {
            $data['detailed_description'] = [];
        }

        foreach ($locales as $locale) {
            $data['title'][$locale] = $this->record->getTranslation('title', $locale, false);
            $data['short_description'][$locale] = $this->record->getTranslation('short_description', $locale, false);
            $data['detailed_description'][$locale] = $this->record->getTranslation('detailed_description', $locale, false);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->dispatch('toastr', [
            'type' => 'success',
            'title' => __('Product updated'),
            'message' => __('The product has been updated successfully.'),
        ]);
    }
}
