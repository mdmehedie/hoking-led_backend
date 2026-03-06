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
        $query = Product::where('status', 'published');

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = $request->get('per_page', 10);

        $products = $query->orderBy('title')->paginate($perPage);

        return $this->okResponse(['products' => ProductResource::collection($products)], __('Products retrieved successfully'));
    }

    public function show($slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->where('status', 'published')->first();

        if (!$product) {
            return $this->notFoundResponse([], __('Product not found'));
        }

        return $this->okResponse(['product' => new ProductResource($product)], __('Product retrieved successfully'));
    }
}
