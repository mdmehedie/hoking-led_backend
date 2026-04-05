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
        static::saved(function ($model) {
            $model->handleMediaCleanup();
        });

        static::deleted(function ($model) {
            $model->handleMediaDeletion();
        });
    }

    protected function handleMediaCleanup(): void
    {
        foreach ($this->getMediaAttributes() as $attribute) {
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
        // Decode JSON strings to arrays for comparison
        $oldValue = $this->decodeValue($oldValue);
        $newValue = $this->decodeValue($newValue);

        // Normalize to arrays
        $oldData = is_array($oldValue) ? $oldValue : ($oldValue ? [$oldValue] : []);
        $newData = is_array($newValue) ? $newValue : ($newValue ? [$newValue] : []);

        // Check if associative (Translations/JSON Objects)
        $isAssociative = $this->isAssociative($oldData) || $this->isAssociative($newData);

        if ($isAssociative) {
            $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));
            foreach ($allKeys as $key) {
                $oldFile = $oldData[$key] ?? null;
                $newFile = $newData[$key] ?? null;

                if ($oldFile && $oldFile !== $newFile) {
                    $this->deleteSingleFile($oldFile);
                }
            }
        } else {
            // Indexed array (Galleries)
            $removed = array_diff($oldData, $newData);
            foreach ($removed as $file) {
                if ($file) {
                    $this->deleteSingleFile($file);
                }
            }
        }
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
