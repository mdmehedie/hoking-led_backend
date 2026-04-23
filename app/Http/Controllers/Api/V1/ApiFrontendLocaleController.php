<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\LocaleResource;
use App\Models\Locale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendLocaleController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $locales = Locale::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('code')
            ->get();

        return $this->okResponse([
            'locales' => LocaleResource::collection($locales),
        ], __('Locales retrieved successfully'));
    }
}
