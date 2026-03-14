<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendCaseStudyController extends Controller
{
    /**
     * Display a listing of case studies.
     */
    public function index(Request $request): View
    {
        $region = $request->route('region');
        
        // Get case studies for the current region
        $caseStudies = CaseStudy::with(['regions'])
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Filter case studies that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        return view('frontend.case-studies.index', compact('caseStudies', 'currentRegion', 'region'));
    }

    /**
     * Display the specified case study.
     */
    public function show(Request $request): View
    {
        // Get parameters from route explicitly
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Find case study that is available in the current region
        $caseStudy = CaseStudy::with(['regions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Ensure case study is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->firstOrFail();

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        // Get alternates for SEO
        $alternates = $caseStudy->getAlternates();
        
        // Get related case studies (same region)
        $relatedCaseStudies = CaseStudy::with(['regions'])
            ->where('status', 'published')
            ->where('id', '!=', $caseStudy->id)
            ->when($region, function ($query, $region) {
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return view('frontend.case-studies.show', compact(
            'caseStudy', 
            'currentRegion', 
            'region', 
            'alternates', 
            'relatedCaseStudies'
        ));
    }
}
