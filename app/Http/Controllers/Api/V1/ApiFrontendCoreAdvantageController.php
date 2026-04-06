<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\CoreAdvantageResource;
use App\Models\CoreAdvantage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendCoreAdvantageController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);

        $advantages = CoreAdvantage::active()
            ->ordered()
            ->paginate($perPage);

        return $this->okResponse(
            ['advantages' => CoreAdvantageResource::collection($advantages)],
            __('Core advantages retrieved successfully')
        );
    }
}
