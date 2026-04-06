<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait HasMedia
{
    /**
     * Disk used for storage operations.
     *
     * @var string
     */
    protected string $mediaDisk = 'public';

    public static function bootHasMedia(): void
    {
        // Run on 'saving' to catch translatable data BEFORE HasTranslations strips it
        static::saving(function ($model) {
            $model->handleMediaCleanup();
        });

        static::deleted(function ($model) {
            $model->handleMediaDeletion();
        });
    }

    protected function handleMediaCleanup(): void
    {
        foreach ($this->getMediaAttributes() as $attribute) {
            // Skip translatable attributes — handled by HasTranslations trait
            if (property_exists($this, 'translatable')
                && is_array($this->translatable)
                && in_array($attribute, $this->translatable)
            ) {
                continue;
            }

            // Dotted notation support: 'slider_data.background_image'
            if (str_contains($attribute, '.')) {
                [$root, $key] = explode('.', $attribute, 2);

                $oldValue = Arr::get($this->getOriginal($root), $key);
                $newValue = Arr::get($this->getAttribute($root), $key);

                $this->compareSimpleValues($oldValue, $newValue);
                continue;
            }

            // Standard attribute handling
            $oldValue = $this->getOriginal($attribute);
            $newValue = $this->getAttribute($attribute);

            $this->compareAndDelete($oldValue, $newValue);
        }
    }

    protected function handleMediaDeletion(): void
    {
        foreach ($this->getMediaAttributes() as $attribute) {
            if (str_contains($attribute, '.')) {
                [$root, $key] = explode('.', $attribute, 2);
                $value = Arr::get($this->getAttribute($root), $key);
                if ($value) {
                    $this->deleteSingleFile($value);
                }
                continue;
            }

            $value = $this->getAttribute($attribute);
            if ($value) {
                $this->deleteFilesFromValue($value);
            }
        }
    }

    protected function compareAndDelete(mixed $oldValue, mixed $newValue): void
    {
        $oldValue = $this->decodeValue($oldValue);
        $newValue = $this->decodeValue($newValue);

        $oldPaths = $this->extractMediaPaths($oldValue);
        $newPaths = $this->extractMediaPaths($newValue);

        $removed = array_diff($oldPaths, $newPaths);
        foreach ($removed as $file) {
            $this->deleteSingleFile($file);
        }
    }

    /**
     * Recursively extract all file paths from a nested structure.
     * Handles: strings, indexed arrays, associative arrays (translations),
     * and deeply nested objects like [{"title": "T", "image": "path.jpg"}].
     */
    protected function extractMediaPaths(mixed $value): array
    {
        if (is_string($value)) {
            return [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        $paths = [];
        foreach ($value as $item) {
            if (is_string($item)) {
                $paths[] = $item;
            } elseif (is_array($item)) {
                $paths = array_merge($paths, $this->extractMediaPaths($item));
            }
        }
        return $paths;
    }

    protected function compareSimpleValues(mixed $oldValue, mixed $newValue): void
    {
        if ($oldValue && $oldValue !== $newValue) {
            $this->deleteSingleFile($oldValue);
        }
    }

    protected function deleteFilesFromValue(mixed $value): void
    {
        $value = $this->decodeValue($value);
        
        if (is_string($value)) {
            $this->deleteSingleFile($value);
        } elseif (is_array($value)) {
            foreach ($value as $file) {
                if (is_string($file)) {
                    $this->deleteSingleFile($file);
                }
            }
        }
    }

    protected function deleteSingleFile(string $file): void
    {
        if (Storage::disk($this->getMediaDisk())->exists($file)) {
            Storage::disk($this->getMediaDisk())->delete($file);
        }
    }

    protected function decodeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return $value;
    }

    protected function isAssociative(array $array): bool
    {
        return !empty($array) && !Arr::isList($array);
    }

    public function getMediaAttributes(): array
    {
        if (!property_exists($this, 'mediaAttributes')) {
            throw new \Exception(
                class_basename($this) . ' must declare "protected array $mediaAttributes" to use the HasMedia trait.'
            );
        }
        return $this->mediaAttributes;
    }

    public function getMediaDisk(): string
    {
        return $this->mediaDisk ?? 'public';
    }
}
