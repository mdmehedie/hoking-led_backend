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

        $products = $query->orderBy('title')->get();

        return $this->okResponse(['products' => ProductResource::collection($products)], __('Products retrieved successfully'));
    }
}
