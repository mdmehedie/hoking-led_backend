<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiFrontendAppSettingController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $appSettings = AppSetting::orderBy('id')->paginate($perPage);

        return $this->okResponse(['app_settings' => AppSettingResource::collection($appSettings)], __('App settings retrieved successfully'));
    }

    public function show($column): JsonResponse
    {
        $appSetting = AppSetting::first(); // Assuming there's only one app setting record

        if (!$appSetting) {
            return $this->notFoundResponse([], 'App setting not found');
        }

        $allowedColumns = array_merge($appSetting->getFillable(), ['contact_emails', 'contact_phones', 'office_addresses', 'social_links']);

        if (!in_array($column, $allowedColumns)) {
            return $this->notFoundResponse([], 'Field not found');
        }

        if (in_array($column, ['contact_emails', 'contact_phones', 'office_addresses', 'social_links'])) {
            $value = $appSetting->organization[$column] ?? null;
        } else {
            $value = $appSetting->$column;

            // If it's organization, return the array
            if ($column === 'organization') {
                $value = $appSetting->organization;
            }
        }

        return $this->okResponse(['value' => $value], __('App setting field retrieved successfully'));
    }
}