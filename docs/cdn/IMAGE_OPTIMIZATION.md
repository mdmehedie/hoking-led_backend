# Image Optimization Guide

## 📋 Overview

This guide covers the comprehensive image optimization system, including WebP/AVIF generation, responsive image creation, and lazy loading implementation.

## 🚀 Features Overview

### **Automatic Format Conversion**
- WebP generation for modern browsers
- AVIF generation for cutting-edge browsers
- Fallback to original formats
- Quality optimization

### **Responsive Image Generation**
- Multiple sizes for different viewports
- Automatic aspect ratio preservation
- Progressive JPEG support
- Optimized file sizes

### **Lazy Loading**
- Intersection Observer API
- Fallback for older browsers
- Progressive loading with placeholders
- Performance monitoring

## 🎨 ImageOptimizer Service

### Core Functionality

The `ImageOptimizer` service handles all image optimization tasks:

```php
// app/Services/ImageOptimizer.php
class ImageOptimizer
{
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
}
```

### WebP Generation

```php
private static function generateWebP($image, $originalPath, $disk)
{
    $storage = Storage::disk($disk);
    $webpPath = self::getWebPPath($originalPath);
    
    try {
        $webp = clone $image;
        $webp->encode('webp', 85); // 85% quality
        
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
```

### AVIF Generation

```php
private static function generateAVIF($image, $originalPath, $disk)
{
    $storage = Storage::disk($disk);
    $avifPath = self::getAVIFPath($originalPath);
    
    try {
        $avif = clone $image;
        $avif->encode('avif', 80); // 80% quality
        
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
```

### Responsive Image Generation

```php
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
```

## 🖼️ Frontend Implementation

### Optimized Image Component

The `optimized-image` Blade component automatically generates picture elements:

```blade
{{-- resources/views/components/optimized-image.blade.php --}}
@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'sizes' => null,
    'disk' => 'public'
])

@php
    use App\Services\ImageOptimizer;
    
    $pictureHtml = ImageOptimizer::generatePictureElement($src, $alt, $class, $loading, $disk);
@endphp

{!! $pictureHtml !!}
```

### Picture Element Generation

```php
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
```

## 🔄 Lazy Loading Implementation

### JavaScript Lazy Loader

The `LazyImageLoader` class handles lazy loading:

```javascript
// public/js/lazy-image-loader.js
class LazyImageLoader {
    constructor(options = {}) {
        this.options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1,
            placeholder: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y0ZjRmNCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5Mb2FkaW5nLi4uPC90ZXh0Pjwvc3ZnPg==',
            ...options
        };
        
        this.init();
    }

    init() {
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            this.setupFallback();
        }
    }

    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, this.options);

        // Observe all lazy images
        document.querySelectorAll('img[data-src]').forEach(img => {
            if (img.src && !img.complete) {
                img.src = this.options.placeholder;
                img.classList.add('lazy-loading');
            }
            observer.observe(img);
        });

        // Observe picture elements
        document.querySelectorAll('picture[data-src]').forEach(picture => {
            const img = picture.querySelector('img');
            if (img && !img.complete) {
                img.src = this.options.placeholder;
                img.classList.add('lazy-loading');
            }
            observer.observe(picture);
        });
    }

    loadImage(element) {
        const src = element.dataset.src;
        
        if (!src) return;

        if (element.tagName === 'IMG') {
            this.loadImgElement(element, src);
        } else if (element.tagName === 'PICTURE') {
            this.loadPictureElement(element, src);
        }
    }

    loadImgElement(img, src) {
        const newImg = new Image();
        
        newImg.onload = () => {
            img.src = src;
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');
            img.removeAttribute('data-src');
            
            // Add fade-in effect
            img.style.opacity = '0';
            setTimeout(() => {
                img.style.transition = 'opacity 0.3s ease-in-out';
                img.style.opacity = '1';
            }, 10);
        };
        
        newImg.onerror = () => {
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-error');
        };
        
        newImg.src = src;
    }
}
```

### Lazy Loading CSS

```css
/* Add to your CSS */
.lazy-loading {
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.lazy-loaded {
    opacity: 1;
    transition: opacity 0.3s ease;
}

.lazy-error {
    opacity: 0.5;
    filter: grayscale(100%);
}

/* Placeholder styling */
img[src*="data:image/svg+xml"] {
    background: #f4f4f4;
    border: 1px dashed #ddd;
}
```

## 🚀 Usage Examples

### Basic Optimized Image

```blade
{{-- Simple usage --}}
<x-optimized-image 
    src="/storage/images/photo.jpg" 
    alt="Beautiful landscape" 
    class="w-full h-auto" 
/>
```

### Advanced Usage

```blade
{{-- With all options --}}
<x-optimized-image 
    src="/storage/images/photo.jpg" 
    alt="Beautiful landscape" 
    class="w-full h-auto rounded-lg shadow-lg" 
    width="800" 
    height="600" 
    loading="lazy" 
    sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
    disk="public"
/>
```

### Manual Picture Element

```blade
{!! ImageOptimizer::generatePictureElement(
    '/storage/images/photo.jpg',
    'Beautiful landscape',
    'w-full h-auto',
    'lazy'
) !!}
```

### Lazy Loading with JavaScript

```html
<!-- Manual lazy loading -->
<img 
    data-src="/storage/images/photo.jpg" 
    alt="Photo" 
    class="lazy-image"
    loading="lazy"
>

<!-- Picture element with lazy loading -->
<picture data-src="/storage/images/photo.jpg">
    <source type="image/webp" data-srcset="/storage/images/photo.webp">
    <source type="image/avif" data-srcset="/storage/images/photo.avif">
    <img data-src="/storage/images/photo.jpg" alt="Photo" class="lazy-image">
</picture>
```

## 🔧 Command Line Tool

### OptimizeImages Command

```bash
# Optimize all images
php artisan media:optimize-images

# Optimize specific disk
php artisan media:optimize-images --disk=public

# Optimize specific path
php artisan media:optimize-images --path=images/products

# Force re-optimization
php artisan media:optimize-images --force

# Dry run (show what would be optimized)
php artisan media:optimize-images --dry-run
```

### Command Implementation

```php
// app/Console/Commands/OptimizeImages.php
class OptimizeImages extends Command
{
    protected $signature = 'media:optimize-images 
                            {--disk=public : The storage disk to optimize}
                            {--path= : Specific path to optimize}
                            {--force : Force re-optimization of existing images}
                            {--dry-run : Show what would be optimized without actually doing it}';

    public function handle()
    {
        $disk = $this->option('disk');
        $path = $this->option('path') ?? '';
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $storage = Storage::disk($disk);
        $images = $storage->allFiles($path);
        $imageFiles = $this->filterImageFiles($images, $storage, $force);
        
        if (empty($imageFiles)) {
            $this->info("No images found to optimize.");
            return;
        }

        $this->info("Found " . count($imageFiles) . " images to process");

        $progressBar = $this->output->createProgressBar(count($imageFiles));
        $progressBar->start();

        $optimized = 0;
        $failed = 0;

        foreach ($imageFiles as $imagePath) {
            try {
                if ($dryRun) {
                    $this->line("Would optimize: {$imagePath}");
                    $optimized++;
                } else {
                    if (ImageOptimizer::optimizeImage($imagePath, $disk)) {
                        $optimized++;
                        $this->line("✓ Optimized: {$imagePath}", null, 'info');
                    } else {
                        $failed++;
                        $this->line("✗ Failed: {$imagePath}", null, 'error');
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                $this->line("✗ Error: {$imagePath} - " . $e->getMessage(), null, 'error');
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Optimization completed!");
        $this->line("Optimized: {$optimized}");
        $this->line("Failed: {$failed}");
    }
}
```

## 📊 Configuration

### Environment Variables

```env
# Image Optimization
IMAGE_OPTIMIZATION_ENABLED=true
IMAGE_QUALITY_WEBP=85
IMAGE_QUALITY_AVIF=80
IMAGE_GENERATE_RESPONSIVE=true

# Supported formats
IMAGE_SUPPORTED_FORMATS=jpg,jpeg,png,gif,bmp,webp

# Responsive image sizes
IMAGE_SIZES_THUMB=300
IMAGE_SIZES_MEDIUM=768
IMAGE_SIZES_LARGE=1200
```

### Service Configuration

```php
// config/image-optimization.php (create if needed)
return [
    'enabled' => env('IMAGE_OPTIMIZATION_ENABLED', true),
    'quality' => [
        'webp' => env('IMAGE_QUALITY_WEBP', 85),
        'avif' => env('IMAGE_QUALITY_AVIF', 80),
        'jpeg' => 85,
        'png' => 90,
    ],
    'responsive' => [
        'enabled' => env('IMAGE_GENERATE_RESPONSIVE', true),
        'sizes' => [
            'thumb' => env('IMAGE_SIZES_THUMB', 300),
            'medium' => env('IMAGE_SIZES_MEDIUM', 768),
            'large' => env('IMAGE_SIZES_LARGE', 1200),
        ],
    ],
    'formats' => [
        'webp' => true,
        'avif' => true,
    ],
];
```

## 🔍 Image Metadata

### Get Image Information

```php
// Get image metadata
$metadata = ImageOptimizer::getImageMetadata('images/photo.jpg', 'public');

// Returns:
[
    'width' => 1920,
    'height' => 1080,
    'mime_type' => 'image/jpeg',
    'size' => 2048576,
    'has_webp' => true,
    'has_avif' => true,
]
```

### Check Optimization Status

```php
// Check if image is optimized
$isOptimized = ImageOptimizer::isOptimized('images/photo.jpg', 'public');

// Get optimized URLs
$webpUrl = ImageOptimizer::getWebPUrl('images/photo.jpg', 'public');
$avifUrl = ImageOptimizer::getAVIFUrl('images/photo.jpg', 'public');
```

## 📈 Performance Monitoring

### Track Optimization Progress

```php
// In your command or service
$stats = [
    'total_images' => count($allImages),
    'optimized_images' => $optimizedCount,
    'failed_images' => $failedCount,
    'space_saved' => $this->calculateSpaceSaved(),
    'optimization_rate' => ($optimizedCount / count($allImages)) * 100,
];

logger()->info('Image optimization completed', $stats);
```

### Monitor Lazy Loading

```javascript
// Track lazy loading performance
window.addEventListener('load', () => {
    const lazyImages = document.querySelectorAll('.lazy-loaded');
    const lazyErrors = document.querySelectorAll('.lazy-error');
    
    // Send analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'lazy_loading_stats', {
            'total_loaded': lazyImages.length,
            'total_errors': lazyErrors.length,
            'success_rate': (lazyImages.length / (lazyImages.length + lazyErrors.length)) * 100
        });
    }
});
```

## 🛠️ Troubleshooting

### Common Issues

#### **WebP/AVIF Generation Fails**
```bash
# Check if required extensions are installed
php -m | grep gd

# Install GD extension
sudo apt-get install php-gd  # Ubuntu/Debian
sudo yum install php-gd        # CentOS/RHEL

# Check Intervention/Image
php artisan tinker
>>> Image::make('test.jpg')->encode('webp')
```

#### **Lazy Loading Not Working**
```javascript
// Check if Intersection Observer is supported
if (!('IntersectionObserver' in window)) {
    console.log('Intersection Observer not supported');
}

// Check if script is loaded
console.log(typeof window.lazyImageLoader);
```

#### **Optimized Images Not Showing**
```bash
# Check if files exist
php artisan tinker
>>> Storage::disk('public')->exists('images/photo.webp')

# Check file permissions
ls -la storage/app/public/images/
```

### Debug Tools

```php
// Enable debug mode
// config/image-optimization.php
'debug' => env('APP_DEBUG', false),

// In your service
if (config('image-optimization.debug')) {
    logger()->info('Optimizing image', ['path' => $imagePath]);
}
```

## 📚 Best Practices

### **Image Optimization**
1. **Choose appropriate quality**: 85% for WebP, 80% for AVIF
2. **Generate multiple sizes**: Thumb, medium, large
3. **Use modern formats**: WebP and AVIF when supported
4. **Implement lazy loading**: For below-the-fold images

### **Performance**
1. **Optimize on upload**: Generate versions immediately
2. **Use CDN**: Serve from Cloudflare
3. **Implement caching**: Cache optimized versions
4. **Monitor performance**: Track load times

### **User Experience**
1. **Add placeholders**: Show loading state
2. **Fade-in effect**: Smooth loading transition
3. **Error handling**: Show fallback on error
4. **Responsive design**: Serve appropriate sizes

### **Storage Management**
1. **Clean up old files**: Remove unused versions
2. **Monitor disk usage**: Track storage consumption
3. **Compress efficiently**: Balance quality vs size
4. **Use appropriate storage**: Local vs cloud

## 🎯 Advanced Features

### **Progressive JPEGs**
```php
// Generate progressive JPEGs
$image->interlace(true);
$image->encode('jpeg', 85);
```

### **Image Compression**
```php
// Use spatie/image-optimizer
Spatie\ImageOptimizer\OptimizerChain::factory()
    ->addOptimizer(new Spatie\ImageOptimizer\OptipngOptimizer())
    ->addOptimizer(new Spatie\ImageOptimizer\JpegoptimOptimizer())
    ->optimize($imagePath);
```

### **Custom Filters**
```php
// Apply custom filters
$image->filter(new \Intervention\Image\Filters\DemoFilter());
$image->brightness(10);
$image->contrast(20);
$image->sharpen(5);
```

## 📞 Support

For image optimization issues:
1. Check the **Image Optimization** command output
2. Verify GD library is installed
3. Check file permissions
4. Monitor storage usage

---

*Last updated: March 7, 2025*
