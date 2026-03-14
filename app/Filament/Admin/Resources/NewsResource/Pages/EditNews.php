<?php

namespace App\Filament\Admin\Resources\NewsResource\Pages;

use App\Filament\Admin\Resources\NewsResource;
use App\Models\Locale;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locales = Locale::activeCodes();

        if (!isset($data['title']) || !is_array($data['title'])) {
            $data['title'] = [];
        }

        if (!isset($data['excerpt']) || !is_array($data['excerpt'])) {
            $data['excerpt'] = [];
        }

        if (!isset($data['content']) || !is_array($data['content'])) {
            $data['content'] = [];
        }

        if (!isset($data['image_path']) || !is_array($data['image_path'])) {
            $data['image_path'] = [];
        }

        foreach ($locales as $locale) {
            $data['title'][$locale] = $this->record->getTranslation('title', $locale, false);
            $data['excerpt'][$locale] = $this->record->getTranslation('excerpt', $locale, false);
            $data['content'][$locale] = $this->record->getTranslation('content', $locale, false);
            $data['image_path'][$locale] = $this->record->getTranslation('image_path', $locale, false);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
