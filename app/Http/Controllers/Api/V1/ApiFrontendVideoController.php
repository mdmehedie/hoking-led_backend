<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendVideoController extends ApiBaseController
{
    /**
     * Get list of videos.
     */
    public function index(Request $request): JsonResponse
    {
        $videos = Video::get();

        return $this->okResponse(
            ['videos' => VideoResource::collection($videos)],
            __('Videos retrieved successfully')
        );
    }

    /**
     * Get a specific video by slug.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $video = Video::where('slug', $slug)
            ->first();

        if (!$video) {
            return $this->notFoundResponse([], __('Video not found'));
        }

        return $this->okResponse(
            ['video' => new VideoResource($video)],
            __('Video retrieved successfully')
        );
    }
}
