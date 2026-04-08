<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendBrandController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);

        $brands = Brand::active()
            ->orderBy('sort_order')
            ->paginate($perPage);

        return $this->okResponse(
            ['brands' => BrandResource::collection($brands)],
            __('Brands retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $brand = Brand::active()
            ->where('slug', $slug)
            ->first();

        if (!$brand) {
            return $this->notFoundResponse([], __('Brand not found'));
        }

        return $this->okResponse(
            ['brand' => new BrandResource($brand)],
            __('Brand retrieved successfully')
        );
    }
}
