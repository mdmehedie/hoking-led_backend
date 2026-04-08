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
        $category = $request->get('category');
        $isTop = $request->boolean('top');

        $products = Product::published()
            ->when($category, fn ($q, $cat) => $q->where('category_id', $cat))
            ->when($isTop, fn ($q) => $q->where('is_top', true))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->okResponse(
            ['products' => ProductResource::collection($products)],
            __('Products retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $product = Product::published()
            ->where('slug', $slug)
            ->first();

        if (!$product) {
            return $this->notFoundResponse([], __('Product not found'));
        }

        return $this->okResponse(
            ['product' => new ProductResource($product)],
            __('Product retrieved successfully')
        );
    }
}
