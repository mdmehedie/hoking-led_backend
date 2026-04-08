<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendBlogController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $recent = $request->boolean('recent');
        $popular = $request->boolean('popular');
        $limit = $request->get('limit');

        $query = Blog::published();

        // Apply filters
        $query->when($recent, fn ($q) => $q->recent($limit ?? 5));
        $query->when($popular && !$recent, fn ($q) => $q->popular());

        // If neither recent nor popular, use default ordering
        if (!$recent && !$popular) {
            $query->orderBy('published_at', 'desc');
        }

        $blogs = $query->paginate($perPage);

        return $this->okResponse(
            ['blogs' => BlogResource::collection($blogs)],
            __('Blogs retrieved successfully')
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $blog = Blog::published()
            ->where('slug', $slug)
            ->first();

        if (!$blog) {
            return $this->notFoundResponse([], __('Blog not found'));
        }

        return $this->okResponse(
            ['blog' => new BlogResource($blog)],
            __('Blog retrieved successfully')
        );
    }
}
