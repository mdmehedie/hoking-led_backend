<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use Illuminate\Http\JsonResponse;

class ApiFrontendCaseStudyController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $caseStudies = CaseStudy::where('status', 'published')->orderBy('published_at', 'desc')->get();

        return $this->okResponse(['case_studies' => CaseStudyResource::collection($caseStudies)], __('Case studies retrieved successfully'));
    }
}
