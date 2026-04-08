<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;

class ApiFrontendTeamMemberController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $members = TeamMember::active()
            ->with(['translations'])
            ->orderBy('sort_order')
            ->get();

        return $this->okResponse(
            ['team_members' => TeamMemberResource::collection($members)],
            __('Team members retrieved successfully')
        );
    }

    public function show(string $slug): JsonResponse
    {
        $member = TeamMember::where('slug', $slug)->where('status', true)->first();

        if (!$member) {
            return $this->notFoundResponse([], __('Team member not found'));
        }

        return $this->okResponse(
            ['team_member' => new TeamMemberResource($member)],
            __('Team member retrieved successfully')
        );
    }
}
