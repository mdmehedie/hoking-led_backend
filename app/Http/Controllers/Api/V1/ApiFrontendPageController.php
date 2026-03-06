<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class ApiFrontendPageController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $pages = Page::where('status', 'published')->orderBy('published_at', 'desc')->paginate($perPage);

        return $this->okResponse(['pages' => PageResource::collection($pages)], __('Pages retrieved successfully'));
    }

    public function show($slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->where('status', 'published')->first();

        if (!$page) {
            return $this->notFoundResponse([], __('Page not found'));
        }

        return $this->okResponse(['page' => new PageResource($page)], __('Page retrieved successfully'));
    }
}
