<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendNewsController extends Controller
{
    /**
     * Display a listing of news articles.
     */
    public function index(Request $request): View
    {
        $region = $request->route('region');
        
        // Get news articles for the current region
        $newsArticles = News::with(['regions'])
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Filter news articles that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        return view('frontend.news.index', compact('newsArticles', 'currentRegion', 'region'));
    }

    /**
     * Display the specified news article.
     */
    public function show(Request $request): View
    {
        // Get parameters from route explicitly
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Find news article that is available in the current region
        $newsArticle = News::with(['regions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Ensure news article is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->firstOrFail();

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        // Get alternates for SEO
        $alternates = $newsArticle->getAlternates();
        
        // Get related news articles (same region)
        $relatedNews = News::with(['regions'])
            ->where('status', 'published')
            ->where('id', '!=', $newsArticle->id)
            ->when($region, function ($query, $region) {
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return view('frontend.news.show', compact(
            'newsArticle', 
            'currentRegion', 
            'region', 
            'alternates', 
            'relatedNews'
        ));
    }
}
