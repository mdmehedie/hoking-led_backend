<?php

namespace App\Traits;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslations
{
    protected array $pendingTranslations = [];

    public static function bootHasTranslations(): void
    {
        static::saving(function ($model): void {
            if (!property_exists($model, 'pendingTranslations') || $model->pendingTranslations === []) {
                return;
            }

            if ($model->exists && !$model->isDirty()) {
                $model->setUpdatedAt($model->freshTimestamp());
            }
        });

        static::saved(function ($model): void {
            if (!property_exists($model, 'pendingTranslations') || $model->pendingTranslations === []) {
                return;
            }

            foreach ($model->pendingTranslations as $locale => $attributes) {
                foreach ($attributes as $attribute => $value) {
                    $model->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                            'attribute' => $attribute,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                }
            }

            $model->pendingTranslations = [];
        });
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getTranslation(string $attribute, ?string $locale = null, bool $fallback = true): mixed
    {
        $locale = $locale ?: app()->getLocale();

        // Ensure translations relationship is loaded
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations
            ->firstWhere(fn ($t) => $t->locale === $locale && $t->attribute === $attribute);

        if ($translation) {
            $value = $translation->value;
            // Try to decode JSON for arrays/objects
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
            return $value;
        }

        if (!$fallback) {
            return null;
        }

        if ($fallback) {
            $default = Locale::defaultCode();
            if ($default !== $locale) {
                $fallbackTranslation = $this->translations
                    ->firstWhere(fn ($t) => $t->locale === $default && $t->attribute === $attribute);

                if ($fallbackTranslation) {
                    $value = $fallbackTranslation->value;
                    // Try to decode JSON for arrays/objects
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            return $decoded;
                        }
                    }
                    return $value;
                }
            }
        }

        return parent::getAttribute($attribute);
    }

    public function setTranslation(string $attribute, string $locale, mixed $value): static
    {
        // JSON encode arrays and objects for storage
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        
        $this->pendingTranslations[$locale][$attribute] = $value;

        $default = Locale::defaultCode();
        if ($locale === $default) {
            parent::setAttribute($attribute, $value);
        }

        return $this;
    }

    public function getAttribute($key): mixed
    {
        if (is_string($key)) {
            // Check for dotted notation like 'title.en'
            if (strpos($key, '.') !== false) {
                [$attribute, $locale] = explode('.', $key, 2);
                if ($this->isTranslatableAttribute($attribute)) {
                    return $this->getTranslation($attribute, $locale);
                }
            } elseif ($this->isTranslatableAttribute($key)) {
                return $this->getTranslation($key);
            }
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value): static
    {
        if (is_string($key)) {
            // Handle dotted notation like 'features.en'
            if (strpos($key, '.') !== false) {
                [$attribute, $locale] = explode('.', $key, 2);
                if ($this->isTranslatableAttribute($attribute)) {
                    return $this->setTranslation($attribute, $locale, $value);
                }
            }
            
            // Handle array of translations (e.g., from Filament form)
            if ($this->isTranslatableAttribute($key)) {
                if (is_array($value)) {
                    foreach ($value as $locale => $translatedValue) {
                        if (!is_string($locale)) {
                            continue;
                        }
                        $this->setTranslation($key, $locale, $translatedValue);
                    }

                    return $this;
                }

                return $this->setTranslation($key, app()->getLocale(), $value);
            }
        }

        return parent::setAttribute($key, $value);
    }

    protected function isTranslatableAttribute(string $attribute): bool
    {
        if (!property_exists($this, 'translatable')) {
            return false;
        }

        $translatable = $this->translatable;

        return is_array($translatable) && in_array($attribute, $translatable, true);
    }
}
