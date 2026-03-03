<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
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
            ->get(['code', 'name', 'direction', 'is_default', 'flag_path']);

        return $this->okResponse([
            'locales' => $locales,
        ], __('Locales retrieved successfully'));
    }
}
