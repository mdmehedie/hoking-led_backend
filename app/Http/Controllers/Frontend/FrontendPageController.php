<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendPageController extends Controller
{
    /**
     * Display the specified page.
     */
    public function show(Request $request): View
    {
        // Get parameters from route explicitly
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Find page that is available in the current region
        $page = Page::with(['regions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->when($region, function ($query, $region) {
                // Ensure page is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->firstOrFail();

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        // Get alternates for SEO
        $alternates = $page->getAlternates();

        return view('frontend.pages.show', compact(
            'page', 
            'currentRegion', 
            'region', 
            'alternates'
        ));
    }
}
