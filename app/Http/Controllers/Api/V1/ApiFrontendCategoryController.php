<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class ApiFrontendCategoryController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $categories = Category::with('parent')->where('is_visible', true)->orderBy('name')->paginate($perPage);

        return $this->okResponse(['categories' => CategoryResource::collection($categories)], __('Categories retrieved successfully'));
    }

    public function show(Request $request): JsonResponse
    {
        $slug = $request->route('slug');
        
        $category = Category::with(['parent', 'children', 'translations'])
            ->where('slug', $slug)
            ->where('is_visible', true)
            ->first();

        if (!$category) {
            return $this->notFoundResponse([], __('Category not found'));
        }

        return $this->okResponse(['category' => new CategoryResource($category)], __('Category retrieved successfully'));
    }
}
