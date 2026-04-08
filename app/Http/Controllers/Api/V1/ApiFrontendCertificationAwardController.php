<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\CertificationAwardResource;
use App\Models\CertificationAward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendCertificationAwardController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $year = $request->get('year');
        $query = CertificationAward::where('is_visible', true)->orderBy('sort_order');

        if ($year) {
            $query->whereYear('date_awarded', $year);
        }

        $certifications = $query->paginate($perPage);

        return $this->okResponse(
            ['certifications' => CertificationAwardResource::collection($certifications)],
            __('Certifications retrieved successfully')
        );
    }

    public function show($slug): JsonResponse
    {
        $certification = CertificationAward::where('slug', $slug)->where('is_visible', true)->first();

        if (!$certification) {
            return $this->notFoundResponse([], __('Certification not found'));
        }

        return $this->okResponse(
            ['certification' => new CertificationAwardResource($certification)],
            __('Certification retrieved successfully')
        );
    }
}
