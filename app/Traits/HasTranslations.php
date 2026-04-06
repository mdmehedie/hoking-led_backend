<?php

namespace App\Traits;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait HasTranslations
{
    /**
     * Static registry to store pending translations by model spl_object_id.
     * This prevents Eloquent from treating it as a database column.
     */
    protected static array $pendingTranslations = [];

    /**
     * Boot the translation trait.
     * 
     * CREATE FLOW:
     * - saving(): Extract translations, set default locale value
     * - saved(): Save translations to DB (model has ID now)
     * 
     * UPDATE FLOW:
     * - saving(): Extract & save translations before model updates
     */
    public static function bootHasTranslations(): void
    {
        // For ALL records: extract translations BEFORE save
        static::saving(function ($model): void {
            static::extractTranslationsFromModel($model);
        });

        // For ALL records: save translations AFTER save
        static::saved(function ($model): void {
            static::saveTranslationsFromModel($model);
        });

        // Clean up registry when model is deleted
        static::deleted(function ($model): void {
            // 1. Delete media files inside translatable fields
            static::cleanupTranslatableMediaOnDelete($model);
            
            // 2. Delete translation records from DB
            $model->translations()->delete();
            
            // 3. Clear static registry
            unset(static::$pendingTranslations[spl_object_id($model)]);
        });
    }

    /**
     * STEP 1: Extract translatable fields from model data.
     *
     * Runs BEFORE model saves to database.
     * Sets default locale value for main column.
     * Stores full translation data in static registry for later.
     */
    protected static function extractTranslationsFromModel($model): void
    {
        if (!property_exists($model, 'translatable') || !is_array($model->translatable)) {
            return;
        }

        $translationsToSave = [];

        foreach ($model->translatable as $attribute) {
            $value = $model->attributes[$attribute] ?? null;

            // Handle JSON strings (common with complex repeaters like video_embeds)
            if (is_string($value) && str_starts_with(trim($value), '{')) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
            }

            // If value is array (multiple locales), extract it
            if (is_array($value)) {
                $translationsToSave[$attribute] = $value;

                // Set default locale value for main column (clean payload)
                $defaultLocale = Locale::defaultCode();
                $defaultValue = $value[$defaultLocale] ?? null;

                // JSON-encode if default value is array/object (structured data like repeaters)
                if (is_array($defaultValue) || is_object($defaultValue)) {
                    $model->attributes[$attribute] = json_encode($defaultValue);
                } else {
                    $model->attributes[$attribute] = $defaultValue;
                }

                // Clean up media for translatable fields
                if (static::hasTranslatableMedia($model, $attribute)) {
                    static::cleanupTranslatableMedia($model, $attribute, $value);
                }
            }
        }

        // Store in static registry (not on model!)
        if (!empty($translationsToSave)) {
            static::$pendingTranslations[spl_object_id($model)] = $translationsToSave;
        }
    }

    /**
     * Check if a translatable attribute has media paths defined.
     */
    protected static function hasTranslatableMedia($model, string $attribute): bool
    {
        if (!property_exists($model, 'translatableMediaKeys')) {
            return false;
        }

        foreach ($model->translatableMediaKeys as $key) {
            if (str_starts_with($key, $attribute . '.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the full original value for a translatable attribute.
     * Merges the main column (default locale) with the translations table.
     */
    protected static function getFullOriginalTranslatable($model, string $attribute): array
    {
        $original = $model->getOriginal($attribute);
        $full = [];

        // 1. Parse main column value (usually default locale)
        if (is_string($original)) {
            $original = json_decode($original, true);
        }

        if (is_array($original)) {
            // If it already has locale keys (e.g., ['en' => ...])
            if (isset($original['en']) || isset($original['bd'])) {
                $full = $original;
            } else {
                // If it's a plain array, assume it's the default locale
                $full[Locale::defaultCode()] = $original;
            }
        }

        // 2. Merge with existing translations from DB
        // Check if the relationship is loaded to avoid extra queries
        if ($model->relationLoaded('translations')) {
            $translations = $model->translations;
        } else {
            $translations = $model->translations()->where('attribute', $attribute)->get();
        }

        foreach ($translations as $t) {
            if ($t->attribute === $attribute) {
                $val = is_string($t->value) ? json_decode($t->value, true) : $t->value;
                $full[$t->locale] = $val;
            }
        }

        return $full;
    }

    /**
     * Handle media cleanup when the model is deleted.
     */
    protected static function cleanupTranslatableMediaOnDelete($model): void
    {
        if (!property_exists($model, 'translatable')) {
            return;
        }

        $disk = $model->getMediaDisk() ?? 'public';
        $dottedKeys = $model->translatableMediaKeys ?? [];

        foreach ($model->translatable as $attribute) {
            // Check if this attribute has media paths defined
            $attributePaths = array_filter($dottedKeys, fn ($key) => str_starts_with($key, $attribute . '.'));
            if (empty($attributePaths)) {
                continue;
            }

            // Get the full value (main column + translations table)
            $value = static::getFullOriginalTranslatable($model, $attribute);
            $paths = static::extractMediaPathsFromNested($value, $attributePaths, $attribute);

            foreach ($paths as $file) {
                Storage::disk($disk)->delete($file);
            }
        }
    }

    /**
     * Compare old vs new translation data and delete removed media files.
     * Uses dotted notation paths from $translatableMediaKeys (e.g., 'detailed_description.*.image').
     */
    protected static function cleanupTranslatableMedia($model, string $attribute, array $newValue): void
    {
        // Get the full original data (Main column + Translations table)
        $oldValue = static::getFullOriginalTranslatable($model, $attribute);

        if (empty($oldValue)) {
            return;
        }

        // Get dotted notation media keys for this attribute (e.g., 'attribute.*.field')
        $dottedKeys = $model->translatableMediaKeys ?? [];
        $attributePaths = array_filter($dottedKeys, fn ($key) => str_starts_with($key, $attribute . '.'));

        if (empty($attributePaths)) {
            return;
        }

        $oldPaths = static::extractMediaPathsFromNested($oldValue, $attributePaths, $attribute);
        $newPaths = static::extractMediaPathsFromNested($newValue, $attributePaths, $attribute);

        $removed = array_diff($oldPaths, $newPaths);
        $disk = $model->getMediaDisk() ?? 'public';

        foreach ($removed as $file) {
            Storage::disk($disk)->delete($file);
        }
    }

    /**
     * Extract file paths from nested translatable data using dotted notation paths.
     * Example path: 'detailed_description.*.image'
     * Data structure: {'en': [{'image': 'path.jpg'}], 'bd': [{'image': 'path2.jpg'}]}
     */
    protected static function extractMediaPathsFromNested(array $data, array $dottedPaths, string $attribute): array
    {
        $paths = [];

        // Extract the sub-path (everything after the attribute name)
        // e.g., 'detailed_description.*.image' → '*.image'
        $prefix = $attribute . '.';
        $subPaths = array_map(fn ($p) => substr($p, strlen($prefix)), $dottedPaths);

        // Iterate through each locale
        foreach ($data as $locale => $items) {
            if (!is_array($items)) {
                continue;
            }

            // Apply each dotted path to the locale data
            foreach ($subPaths as $subPath) {
                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    // Remove wildcard (*) and get the target key
                    $targetKey = str_replace('*.', '', $subPath);
                    $value = Arr::get($item, $targetKey);

                    if (is_string($value) && !empty($value)) {
                        $paths[] = $value;
                    }
                }
            }
        }

        return $paths;
    }

    /**
     * STEP 2: Save translations to database.
     * 
     * Runs AFTER model saves (ID exists for new records).
     */
    protected static function saveTranslationsFromModel($model): void
    {
        $objectId = spl_object_id($model);
        
        if (!isset(static::$pendingTranslations[$objectId])) {
            return;
        }

        $translationsToSave = static::$pendingTranslations[$objectId];

        foreach ($translationsToSave as $attribute => $translations) {
            static::storeTranslations($model, $attribute, $translations);
        }

        // Clean up registry
        unset(static::$pendingTranslations[$objectId]);
    }

    /**
     * Store translations for an attribute.
     */
    protected static function storeTranslations($model, string $attribute, array $translations): void
    {
        foreach ($translations as $locale => $value) {
            if (!is_string($locale)) {
                continue;
            }

            // Encode arrays/objects as JSON
            $storedValue = $value;
            if (is_array($value) || is_object($value)) {
                $storedValue = json_encode($value);
            }

            $model->translations()->updateOrCreate(
                [
                    'locale' => $locale,
                    'attribute' => $attribute,
                ],
                [
                    'value' => $storedValue,
                    'type' => static::determineTranslationType($storedValue),
                ]
            );
        }
    }

    /**
     * STEP 3-4: Model relationship for translations.
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * STEP 4 (Edit Flow): Get translation for a specific locale.
     * 
     * Trait says: "You got something? Let me see..."
     */
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

        // Fallback to default locale
        $default = Locale::defaultCode();
        if ($default !== $locale) {
            $fallbackTranslation = $this->translations
                ->firstWhere(fn ($t) => $t->locale === $default && $t->attribute === $attribute);

            if ($fallbackTranslation) {
                $value = $fallbackTranslation->value;

                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }
                }

                return $value;
            }
        }

        return parent::getAttribute($attribute);
    }

    /**
     * STEP 5 (Edit Flow): Return attributes with translations in locale format.
     * 
     * Filament calls attributesToArray() to fill forms.
     * We return: ['title' => ['en' => 'Hello', 'bd' => 'হ্যালো']]
     * 
     * This eliminates the need for mutateFormDataBeforeFill().
     */
    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        // Add translations as locale arrays
        if (property_exists($this, 'translatable') && is_array($this->translatable)) {
            foreach ($this->translatable as $attribute) {
                $attributes[$attribute] = $this->getTranslationsAsArray($attribute);
            }
        }

        return $attributes;
    }

    /**
     * Get all translations for an attribute as locale array.
     */
    protected function getTranslationsAsArray(string $attribute): array
    {
        // Ensure translations relationship is loaded
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translations = [];

        foreach ($this->translations as $translation) {
            if ($translation->attribute === $attribute) {
                $value = $translation->value;

                // Decode JSON if needed
                if (is_string($value) && json_decode($value, true) !== null) {
                    $value = json_decode($value, true);
                }

                $translations[$translation->locale] = $value;
            }
        }

        return $translations;
    }

    /**
     * Backward compatibility: Set translation directly.
     */
    public function setTranslation(string $attribute, string $locale, mixed $value): static
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $this->translations()->updateOrCreate(
            ['locale' => $locale, 'attribute' => $attribute],
            ['value' => $value, 'type' => static::determineTranslationType($value)]
        );

        return $this;
    }

    /**
     * STEP 6 (Edit Flow): Intercept attribute access.
     * 
     * When Filament accesses $model->title, returns current locale value.
     */
    public function getAttribute($key): mixed
    {
        if (is_string($key)) {
            // Handle dotted notation like 'title.en'
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

    /**
     * STEP 2 (Create Flow): Intercept attribute setting.
     * 
     * When Filament sets $model->title.en = '...', collects into array.
     */
    public function setAttribute($key, $value): static
    {
        if (is_string($key)) {
            // Handle dotted notation like 'title.en'
            if (strpos($key, '.') !== false) {
                [$attribute, $locale] = explode('.', $key, 2);

                if ($this->isTranslatableAttribute($attribute)) {
                    // Collect into array format
                    $current = $this->getAttributeValue($attribute);

                    if (!is_array($current)) {
                        $current = [];
                    }

                    $current[$locale] = $value;

                    return parent::setAttribute($attribute, $current);
                }
            }

            // Handle array of translations (e.g., from Filament form)
            if ($this->isTranslatableAttribute($key)) {
                if (is_array($value)) {
                    // Check if it's locale-based array (keys are locale codes)
                    $isLocaleArray = collect($value)->keys()->every(fn ($k) => is_string($k) && strlen($k) <= 10);

                    if ($isLocaleArray) {
                        return parent::setAttribute($key, $value);
                    }
                }
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Check if attribute is translatable.
     */
    protected function isTranslatableAttribute(string $attribute): bool
    {
        if (!property_exists($this, 'translatable')) {
            return false;
        }

        $translatable = $this->translatable;

        return is_array($translatable) && in_array($attribute, $translatable, true);
    }

    /**
     * Determine the type of translation value for storage.
     */
    protected static function determineTranslationType(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return 'json';
        }

        if (is_string($value)) {
            // Check if it's a file path
            if (str_starts_with($value, '/') || preg_match('/\.(jpg|jpeg|png|gif|webp|svg|pdf|doc|docx)$/i', $value)) {
                return 'file_path';
            }

            // Check if it contains HTML tags (rich text)
            if (strip_tags($value) !== $value) {
                return 'rich_text';
            }

            // Long text vs short string
            if (strlen($value) > 255) {
                return 'text';
            }

            return 'string';
        }

        return 'string';
    }
}
