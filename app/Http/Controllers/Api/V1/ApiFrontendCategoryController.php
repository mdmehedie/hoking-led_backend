<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class ApiFrontendCategoryController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $categories = Category::where('status', true)->orderBy('name')->get();

        return $this->okResponse(['categories' => CategoryResource::collection($categories)], __('Categories retrieved successfully'));
    }
}
