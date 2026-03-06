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

        $caseStudies = CaseStudy::where('status', 'published')->orderBy('published_at', 'desc')->paginate($perPage);

        return $this->okResponse(['case_studies' => CaseStudyResource::collection($caseStudies)], __('Case studies retrieved successfully'));
    }

    public function show($slug): JsonResponse
    {
        $caseStudy = CaseStudy::where('slug', $slug)->where('status', 'published')->first();

        if (!$caseStudy) {
            return $this->notFoundResponse([], __('Case study not found'));
        }

        return $this->okResponse(['case_study' => new CaseStudyResource($caseStudy)], __('Case study retrieved successfully'));
    }
}
