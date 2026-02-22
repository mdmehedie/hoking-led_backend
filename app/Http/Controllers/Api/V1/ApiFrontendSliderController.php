<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class ApiFrontendSliderController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $sliders = Slider::where('status', true)->orderBy('order')->get();

        return $this->okResponse(['sliders' => $sliders], __('Sliders retrieved successfully'));
    }
}
