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
        $region = $request->route('region');

        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }

        $news = News::with(['regions'])
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Filter news that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return $this->okResponse(['news' => NewsResource::collection($news)], __('News retrieved successfully'));
    }

    public function show(Request $request): JsonResponse
    {
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }
        
        $newsItem = News::with(['regions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Ensure news is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->first();

        if (!$newsItem) {
            return $this->notFoundResponse([], __('News not found'));
        }

        return $this->okResponse(['news' => new NewsResource($newsItem)], __('News retrieved successfully'));
    }

    /**
     * Set locale based on region
     */
    private function setLocaleForRegion(string $region): void
    {
        $regionToLocale = [
            'us' => 'en',
            'uk' => 'en-GB', 
            'eu' => 'en',
            'ca' => 'en-CA',
            'au' => 'en-AU',
            'bd' => 'bd'  // Use 'bd' locale code since that's what's in the database
        ];

        $locale = $regionToLocale[$region] ?? 'en';
        app()->setLocale($locale);
    }
}
