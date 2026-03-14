<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog{{ $currentRegion ? ' | ' . $currentRegion->name : '' }}</title>
    
    <!-- Basic Styling -->
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        .region-info { background: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .blog-card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .blog-card h3 { margin-top: 0; }
        .blog-card a { text-decoration: none; color: #333; }
        .blog-card a:hover { text-decoration: underline; }
        .excerpt { color: #666; margin: 10px 0; }
        .meta { font-size: 0.9em; color: #888; }
        .pagination { margin-top: 30px; text-align: center; }
        .pagination a { padding: 8px 16px; margin: 0 4px; text-decoration: none; border: 1px solid #ddd; border-radius: 4px; }
        .pagination a:hover { background: #f5f5f5; }
        .pagination .current { background: #007bff; color: white; border-color: #007bff; }
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

    <!-- Header -->
    <header>
        <h1>Blog{{ $currentRegion ? ' - ' . $currentRegion->name : '' }}</h1>
        <p>{{ $blogs->total() }} articles found</p>
    </header>

    <!-- Blog Grid -->
    @if($blogs->count() > 0)
        <div class="blog-grid">
            @foreach($blogs as $blog)
                <div class="blog-card">
                    <h3>
                        <a href="{{ $region ? '/' . $region . '/blog/' . $blog->slug : '/blog/' . $blog->slug }}">
                            {{ $blog->title }}
                        </a>
                    </h3>
                    
                    @if($blog->excerpt)
                        <div class="excerpt">{{ Str::limit($blog->excerpt, 150) }}</div>
                    @endif
                    
                    <div class="meta">
                        Published: {{ $blog->published_at->format('M j, Y') }}
                        @if($blog->author) | By: {{ $blog->author->name }} @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $blogs->links() }}
        </div>
    @else
        <p>No blog articles found for this region.</p>
    @endif

    <!-- Region Navigation -->
    @if($currentRegion)
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>Switch Region:</strong></p>
            <a href="/blog">Default (US)</a> |
            <a href="/uk/blog">United Kingdom</a> |
            <a href="/eu/blog">European Union</a> |
            <a href="/ca/blog">Canada</a> |
            <a href="/au/blog">Australia</a> |
            <a href="/bd/blog">Bangladesh</a>
        </div>
    @endif
</body>
</html>
