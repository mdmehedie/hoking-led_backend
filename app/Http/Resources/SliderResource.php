<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale),
            'description' => $this->getTranslation('description', $locale),
            'label' => $this->getTranslation('label', $locale),
            'primary_button_text' => $this->getTranslation('primary_button_text', $locale),
            'primary_button_link' => $this->primary_button_link,
            'background_image' => $this->background_image ? url(Storage::url($this->background_image)) : null,
            'foreground_image' => $this->foreground_image ? url(Storage::url($this->foreground_image)) : null,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get translation for a given attribute and locale.
     * Falls back to the default locale if translation is missing.
     */
    protected function getTranslation(string $attribute, string $locale): ?string
    {
        // If model has the translation relationship loaded
        if (method_exists($this, 'translations')) {
            $translation = $this->translations
                ->where('attribute', $attribute)
                ->where('locale', $locale)
                ->first();

            if ($translation) {
                return $translation->value;
            }
        }

        // Fallback: return the attribute's value directly (default locale)
        return $this->{$attribute};
    }
}
