<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class ApiFrontendFeaturedProductController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $featured = Product::where('is_featured', true)->where('status', 'published')->paginate($perPage);

        return $this->okResponse(['featured_products' => ProductResource::collection($featured)], __('Featured products retrieved successfully'));
    }
}
