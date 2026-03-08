<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $region = $request->route('region');
        
        // Get products for the current region
        $products = Product::with(['category', 'regions'])
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Filter products that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        return view('frontend.products.index', compact('products', 'currentRegion', 'region'));
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request): View
    {
        // Get parameters from route explicitly
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Find product that is available in the current region
        $product = Product::with(['category', 'regions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Ensure product is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->firstOrFail();

        // Get region info for display
        $currentRegion = $region ? Region::where('code', $region)->first() : null;
        
        // Get alternates for SEO
        $alternates = $product->getAlternates();
        
        // Get related products (same region)
        $relatedProducts = Product::with(['category', 'regions'])
            ->where('status', 'published')
            ->where('id', '!=', $product->id)
            ->when($region, function ($query, $region) {
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('frontend.products.show', compact(
            'product', 
            'currentRegion', 
            'region', 
            'alternates', 
            'relatedProducts'
        ));
    }
}
