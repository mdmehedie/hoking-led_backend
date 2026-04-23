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

        $caseStudies = CaseStudy::with('category')
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return $this->okResponse(
            ['case_studies' => CaseStudyResource::collection($caseStudies)],
            __('Case studies retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $caseStudy = CaseStudy::with(['category', 'regions'])
            ->published()
            ->where('slug', $slug)
            ->first();

        if (!$caseStudy) {
            return $this->notFoundResponse([], __('Case study not found'));
        }

        return $this->okResponse(
            ['case_study' => new CaseStudyResource($caseStudy)],
            __('Case study retrieved successfully')
        );
    }
}
