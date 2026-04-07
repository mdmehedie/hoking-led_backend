<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendProjectController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $featured = $request->boolean('featured');
        $popular = $request->boolean('popular');
        $successful = $request->boolean('successful');

        $projects = Project::published()
            ->when($featured, fn ($q) => $q->featured())
            ->when($popular, fn ($q) => $q->popular())
            ->when($successful, fn ($q) => $q->successful())
            ->ordered()
            ->paginate($perPage);

        return $this->okResponse(
            ['projects' => ProjectResource::collection($projects)],
            __('Projects retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $project = Project::published()
            ->where('slug', $slug)
            ->first();

        if (!$project) {
            return $this->notFoundResponse([], __('Project not found'));
        }

        return $this->okResponse(
            ['project' => new ProjectResource($project)],
            __('Project retrieved successfully')
        );
    }
}
