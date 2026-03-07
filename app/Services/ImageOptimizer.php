<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Optimize an image and generate WebP/AVIF versions.
     */
    public static function optimizeImage($imagePath, $disk = 'public')
    {
        $storage = Storage::disk($disk);
        
        if (!$storage->exists($imagePath)) {
            return false;
        }

        $image = Image::make($storage->path($imagePath));
        
        // Generate optimized versions
        self::generateWebP($image, $imagePath, $disk);
        self::generateAVIF($image, $imagePath, $disk);
        self::generateResponsiveImages($image, $imagePath, $disk);
        
        return true;
    }

    /**
     * Generate WebP version of the image.
     */
    private static function generateWebP($image, $originalPath, $disk)
    {
        $storage = Storage::disk($disk);
        $webpPath = self::getWebPPath($originalPath);
        
        try {
            $webp = clone $image;
            $webp->encode('webp', 85);
            
            $storage->put($webpPath, $webp->getEncoded());
            
            return $webpPath;
        } catch (\Exception $e) {
            logger()->error('WebP generation failed', [
                'path' => $originalPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate AVIF version of the image.
     */
    private static function generateAVIF($image, $originalPath, $disk)
    {
        $storage = Storage::disk($disk);
        $avifPath = self::getAVIFPath($originalPath);
        
        try {
            $avif = clone $image;
            $avif->encode('avif', 80);
            
            $storage->put($avifPath, $avif->getEncoded());
            
            return $avifPath;
        } catch (\Exception $e) {
            logger()->error('AVIF generation failed', [
                'path' => $originalPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate responsive image sizes.
     */
    private static function generateResponsiveImages($image, $originalPath, $disk)
    {
        $storage = Storage::disk($disk);
        $sizes = [
            'thumb' => 300,
            'medium' => 768,
            'large' => 1200,
        ];
        
        $responsivePaths = [];
        
        foreach ($sizes as $size => $width) {
            $responsivePath = self::getResponsivePath($originalPath, $size);
            
            try {
                $resized = clone $image;
                $resized->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                $storage->put($responsivePath, $resized->getEncoded());
                $responsivePaths[$size] = $responsivePath;
                
                // Also generate WebP versions
                $webpPath = self::getWebPPath($responsivePath);
                $resized->encode('webp', 85);
                $storage->put($webpPath, $resized->getEncoded());
                
            } catch (\Exception $e) {
                logger()->error("Responsive image generation failed for {$size}", [
                    'path' => $originalPath,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $responsivePaths;
    }

    /**
     * Get WebP path for an image.
     */
    public static function getWebPPath($originalPath)
    {
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        return str_replace(".{$extension}", '.webp', $originalPath);
    }

    /**
     * Get AVIF path for an image.
     */
    public static function getAVIFPath($originalPath)
    {
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        return str_replace(".{$extension}", '.avif', $originalPath);
    }

    /**
     * Get responsive image path.
     */
    public static function getResponsivePath($originalPath, $size)
    {
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $filename = pathinfo($originalPath, PATHINFO_FILENAME);
        $directory = pathinfo($originalPath, PATHINFO_DIRNAME);
        
        return "{$directory}/{$filename}-{$size}.{$extension}";
    }

    /**
     * Get the best image URL based on browser support.
     */
    public static function getOptimizedUrl($originalUrl, $disk = 'public')
    {
        $storage = Storage::disk($disk);
        $path = str_replace($storage->url(''), '', $originalUrl);
        
        // Check if WebP version exists
        $webpPath = self::getWebPPath($path);
        if ($storage->exists($webpPath)) {
            return $storage->url($webpPath);
        }
        
        // Check if AVIF version exists
        $avifPath = self::getAVIFPath($path);
        if ($storage->exists($avifPath)) {
            return $storage->url($avifPath);
        }
        
        return $originalUrl;
    }

    /**
     * Generate picture element with multiple sources.
     */
    public static function generatePictureElement($originalUrl, $alt = '', $class = '', $loading = 'lazy', $disk = 'public')
    {
        $storage = Storage::disk($disk);
        $path = str_replace($storage->url(''), '', $originalUrl);
        
        $sources = [];
        
        // AVIF source
        $avifPath = self::getAVIFPath($path);
        if ($storage->exists($avifPath)) {
            $sources[] = '<source type="image/avif" srcset="' . $storage->url($avifPath) . '">';
        }
        
        // WebP source
        $webpPath = self::getWebPPath($path);
        if ($storage->exists($webpPath)) {
            $sources[] = '<source type="image/webp" srcset="' . $storage->url($webpPath) . '">';
        }
        
        // Responsive sources
        foreach (['large', 'medium', 'thumb'] as $size) {
            $responsivePath = self::getResponsivePath($path, $size);
            if ($storage->exists($responsivePath)) {
                $media = $size === 'thumb' ? '(max-width: 768px)' : 
                        ($size === 'medium' ? '(max-width: 1200px)' : '(min-width: 1201px)');
                
                $webpResponsivePath = self::getWebPPath($responsivePath);
                if ($storage->exists($webpResponsivePath)) {
                    $sources[] = '<source type="image/webp" media="' . $media . '" srcset="' . $storage->url($webpResponsivePath) . '">';
                }
                
                $sources[] = '<source media="' . $media . '" srcset="' . $storage->url($responsivePath) . '">';
            }
        }
        
        // Build picture element
        $picture = '<picture>';
        $picture .= implode("\n    ", $sources);
        $picture .= '<img src="' . $originalUrl . '" alt="' . $alt . '" class="' . $class . '" loading="' . $loading . '">';
        $picture .= '</picture>';
        
        return $picture;
    }

    /**
     * Optimize existing images in storage.
     */
    public static function optimizeExistingImages($disk = 'public', $path = '')
    {
        $storage = Storage::disk($disk);
        $images = $storage->allFiles($path);
        
        $optimized = 0;
        $failed = 0;
        
        foreach ($images as $imagePath) {
            if (self::isImage($imagePath)) {
                if (self::optimizeImage($imagePath, $disk)) {
                    $optimized++;
                } else {
                    $failed++;
                }
            }
        }
        
        return [
            'optimized' => $optimized,
            'failed' => $failed,
            'total' => $optimized + $failed
        ];
    }

    /**
     * Check if file is an image.
     */
    private static function isImage($path)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        return in_array($extension, $imageExtensions);
    }

    /**
     * Get image metadata.
     */
    public static function getImageMetadata($imagePath, $disk = 'public')
    {
        $storage = Storage::disk($disk);
        
        if (!$storage->exists($imagePath)) {
            return null;
        }

        $image = Image::make($storage->path($imagePath));
        
        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'mime_type' => $image->mime(),
            'size' => $storage->size($imagePath),
            'has_webp' => $storage->exists(self::getWebPPath($imagePath)),
            'has_avif' => $storage->exists(self::getAVIFPath($imagePath)),
        ];
    }
}
