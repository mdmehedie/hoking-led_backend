<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $blog->title }} - Blog{{ $currentRegion ? ' | ' . $currentRegion->name : '' }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 160) }}">
    <meta name="keywords" content="{{ $blog->meta_keywords ?? '' }}">
    
    <!-- Hreflang Tags -->
    @foreach($alternates as $alternate)
        <link rel="alternate" hreflang="{{ $alternate['locale'] }}" href="{{ $alternate['url'] }}">
    @endforeach
    <link rel="canonical" href="{{ $blog->getUrl() }}">
    
    <!-- Basic Styling -->
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .region-info { background: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .blog-content { line-height: 1.6; }
        .related-blogs { margin-top: 40px; }
        .related-item { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .alternates { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .alternates h3 { margin-top: 0; }
        .alternates ul { list-style-type: none; padding: 0; }
        .alternates li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <!-- Region Information -->
    @if($currentRegion)
        <div class="region-info">
            <strong>Current Region:</strong> {{ $currentRegion->name }} ({{ $currentRegion->code }})
            @if($currentRegion->currency) | Currency: {{ $currentRegion->currency }} @endif
            @if($currentRegion->language) | Language: {{ $currentRegion->language }} @endif
        </div>
    @endif

    <!-- Blog Content -->
    <article>
        <h1>{{ $blog->title }}</h1>
        
        @if($blog->excerpt)
            <p class="excerpt">{{ $blog->excerpt }}</p>
        @endif
        
        <div class="blog-content">
            {!! $blog->content !!}
        </div>
        
        <div class="meta">
            <small>
                Published: {{ $blog->published_at->format('F j, Y') }} 
                @if($blog->author) | By: {{ $blog->author->name }} @endif
            </small>
        </div>
    </article>

    <!-- International SEO Alternates -->
    @if($alternates && count($alternates) > 1)
        <div class="alternates">
            <h3>Available in Other Regions:</h3>
            <ul>
                @foreach($alternates as $alternate)
                    <li>
                        <a href="{{ $alternate['url'] }}" hreflang="{{ $alternate['locale'] }}">
                            {{ strtoupper($alternate['locale']) }} - {{ $alternate['url'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Related Blogs -->
    @if($relatedBlogs && $relatedBlogs->count() > 0)
        <div class="related-blogs">
            <h2>Related {{ $currentRegion ? $currentRegion->name . ' ' : '' }}Blogs</h2>
            @foreach($relatedBlogs as $relatedBlog)
                <div class="related-item">
                    <h3><a href="{{ $region ? '/' . $region . '/blog/' . $relatedBlog->slug : '/blog/' . $relatedBlog->slug }}">
                        {{ $relatedBlog->title }}
                    </a></h3>
                    @if($relatedBlog->excerpt)
                        <p>{{ Str::limit($relatedBlog->excerpt, 100) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Navigation -->
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
        <a href="{{ $region ? '/' . $region . '/blog' : '/blog' }}">← Back to Blog</a>
    </div>
</body>
</html>
