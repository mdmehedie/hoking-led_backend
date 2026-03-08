<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FrontendBlogController extends Controller
{
    /**
     * Display a listing of blogs.
     */
    public function index(Request $request): View
    {
        $region = $request->route('region');
        
        // Get blogs for the current region
        $blogs = Blog::with(['author', 'regions'])
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Filter blogs that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        return view('frontend.blog.index', compact('blogs', 'currentRegion', 'region'));
    }

    /**
     * Display the specified blog.
     */
    public function show(Request $request): View|Response
    {
        // Get parameters from route explicitly
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Find blog that is available in the current region
        $blog = Blog::with(['author', 'regions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Ensure blog is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->firstOrFail();

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        // Get alternates for SEO
        $alternates = $blog->getAlternates();
        
        // Get related blogs (same region)
        $relatedBlogs = Blog::with(['author', 'regions'])
            ->where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->when($region, function ($query, $region) {
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return view('frontend.blog.show', compact(
            'blog', 
            'currentRegion', 
            'region', 
            'alternates', 
            'relatedBlogs'
        ));
    }
}
