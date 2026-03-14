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
    
    $storage = \Illuminate\Support\Facades\Storage::disk($disk);
    $path = str_replace($storage->url(''), '', $src);
    
    // Generate picture element with optimized sources
    $pictureHtml = ImageOptimizer::generatePictureElement($src, $alt, $class, $loading, $disk);
@endphp

{!! $pictureHtml !!}
