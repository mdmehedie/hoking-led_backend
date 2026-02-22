<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;

class ApiFrontendNewsController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $news = News::where('status', 'published')->orderBy('published_at', 'desc')->get();

        return $this->okResponse(['news' => NewsResource::collection($news)], __('News retrieved successfully'));
    }
}
