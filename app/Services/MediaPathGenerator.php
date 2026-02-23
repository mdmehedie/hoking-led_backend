<?php

namespace App\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive/';
    }

    protected function getBasePath(Media $media): string
    {
        if (!$media->model_type) {
            return 'editor-images';
        }
        $modelType = class_basename($media->model_type);
        return strtolower($modelType) . '/' . $media->model_id;
    }
}
