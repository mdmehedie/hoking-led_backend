<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\CaseStudyCategoryResource;
use App\Models\CaseStudyCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendCaseStudyCategoryController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $categories = CaseStudyCategory::with('parent')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);

        return $this->okResponse(
            ['categories' => CaseStudyCategoryResource::collection($categories)],
            __('Case study categories retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $category = CaseStudyCategory::with(['parent', 'children'])
            ->where('slug', $slug)
            ->where('is_visible', true)
            ->first();

        if (!$category) {
            return $this->notFoundResponse([], __('Category not found'));
        }

        return $this->okResponse(
            ['category' => new CaseStudyCategoryResource($category)],
            __('Category retrieved successfully')
        );
    }
}
