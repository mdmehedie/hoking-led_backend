<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ApiFrontendFeaturedProductController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $featured = Product::where('is_featured', true)->where('status', 'published')->get();

        return $this->okResponse(['featured_products' => ProductResource::collection($featured)], __('Featured products retrieved successfully'));
    }
}
