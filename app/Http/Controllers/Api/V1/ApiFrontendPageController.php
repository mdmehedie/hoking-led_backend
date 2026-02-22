<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class ApiFrontendPageController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $pages = Page::where('status', 'published')->orderBy('published_at', 'desc')->get();

        return $this->okResponse(['pages' => PageResource::collection($pages)], __('Pages retrieved successfully'));
    }
}
