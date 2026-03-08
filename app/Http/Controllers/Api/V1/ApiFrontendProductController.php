<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendProductController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $region = $request->route('region');

        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }

        $products = Product::with(['category', 'regions'])
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Filter products that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->okResponse(['products' => ProductResource::collection($products)], __('Products retrieved successfully'));
    }

    public function show(Request $request): JsonResponse
    {
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }
        
        $product = Product::with(['category', 'regions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Ensure product is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->first();

        if (!$product) {
            return $this->notFoundResponse([], __('Product not found'));
        }

        return $this->okResponse(['product' => new ProductResource($product)], __('Product retrieved successfully'));
    }

    /**
     * Set locale based on region
     */
    private function setLocaleForRegion(string $region): void
    {
        $regionToLocale = [
            'us' => 'en',
            'uk' => 'en-GB', 
            'eu' => 'en',
            'ca' => 'en-CA',
            'au' => 'en-AU',
            'bd' => 'bd'  // Use 'bd' locale code since that's what's in the database
        ];

        $locale = $regionToLocale[$region] ?? 'en';
        app()->setLocale($locale);
    }
}
