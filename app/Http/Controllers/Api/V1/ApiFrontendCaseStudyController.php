<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class ApiFrontendCaseStudyController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $region = $request->route('region');

        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }

        $caseStudies = CaseStudy::with(['regions'])
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Filter case studies that are available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return $this->okResponse(['case_studies' => CaseStudyResource::collection($caseStudies)], __('Case studies retrieved successfully'));
    }

    public function show(Request $request): JsonResponse
    {
        $region = $request->route('region');
        $slug = $request->route('slug');
        
        // Set locale based on region
        if ($region) {
            $this->setLocaleForRegion($region);
        }
        
        $caseStudy = CaseStudy::with(['regions'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->when($region, function ($query, $region) {
                // Ensure case study is available in this region
                $query->whereHas('regions', function ($q) use ($region) {
                    $q->where('code', $region)->where('is_active', true);
                });
            })
            ->first();

        if (!$caseStudy) {
            return $this->notFoundResponse([], __('Case study not found'));
        }

        return $this->okResponse(['case_study' => new CaseStudyResource($caseStudy)], __('Case study retrieved successfully'));
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
