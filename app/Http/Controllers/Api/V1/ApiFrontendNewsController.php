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
        $recent = $request->boolean('recent');
        $popular = $request->boolean('popular');
        $limit = $request->get('limit');

        $query = News::published();

        // Apply filters
        $query->when($recent, fn ($q) => $q->recent($limit ?? 5));
        $query->when($popular && !$recent, fn ($q) => $q->popular());

        // If neither recent nor popular, use default ordering
        if (!$recent && !$popular) {
            $query->orderBy('published_at', 'desc');
        }

        $news = $query->paginate($perPage);

        return $this->okResponse(
            ['news' => NewsResource::collection($news)],
            __('News retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $newsItem = News::published()
            ->where('slug', $slug)
            ->first();

        if (!$newsItem) {
            return $this->notFoundResponse([], __('News not found'));
        }

        return $this->okResponse(
            ['news' => new NewsResource($newsItem)],
            __('News retrieved successfully')
        );
    }
}
