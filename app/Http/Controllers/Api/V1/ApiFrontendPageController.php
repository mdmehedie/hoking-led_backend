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
        $region = $request->route('region');

        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }

        $pages = Page::with(['regions'])
            ->where('is_active', true)
            ->when($region, function ($query, $region) {
                // Filter pages that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->okResponse(['pages' => PageResource::collection($pages)], __('Pages retrieved successfully'));
    }

    public function show(Request $request): JsonResponse
    {
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }
        
        $page = Page::with(['regions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->when($region, function ($query, $region) {
                // Ensure page is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->first();

        if (!$page) {
            return $this->notFoundResponse([], __('Page not found'));
        }

        return $this->okResponse(['page' => new PageResource($page)], __('Page retrieved successfully'));
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
