<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\ProductMenuResource;
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
            ->with(['category', 'relatedProducts'])
            ->when($category, fn ($q, $cat) => $q->where('category_id', $cat))
            ->when($isTop, fn ($q) => $q->where('is_top', true))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->okResponse(
            ['products' => ProductResource::collection($products)],
            __('Products retrieved successfully')
        );
    }

    public function menu(): JsonResponse
    {
        $products = Product::published()
            ->select(['id', 'title', 'slug', 'short_description', 'main_image', 'order_column', 'category_id', 'updated_at'])
            ->with('category:id,name,slug')
            ->orderBy('order_column')
            ->get();

        return $this->okResponse(
            ['products' => ProductMenuResource::collection($products)],
            __('Products retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $product = Product::published()
            ->with(['category', 'relatedProducts'])
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
