<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class ApiFrontendNewsController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $news = News::where('status', 'published')->orderBy('published_at', 'desc')->paginate($perPage);

        return $this->okResponse(['news' => NewsResource::collection($news)], __('News retrieved successfully'));
    }

    public function show($slug): JsonResponse
    {
        $newsItem = News::where('slug', $slug)->where('status', 'published')->first();

        if (!$newsItem) {
            return $this->notFoundResponse([], __('News not found'));
        }

        return $this->okResponse(['news' => new NewsResource($newsItem)], __('News retrieved successfully'));
    }
}
