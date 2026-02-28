<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendTestimonialController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $testimonials = Testimonial::visible()
            ->ordered()
            ->paginate($perPage);

        return $this->okResponse(['testimonials' => $testimonials], __('Testimonials retrieved successfully'));
    }
}
