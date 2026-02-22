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

        $blogs = Blog::where('status', 'published')->orderBy('published_at', 'desc')->paginate($perPage);

        return $this->okResponse(['blogs' => BlogResource::collection($blogs)], __('Blogs retrieved successfully'));
    }

    public function show($slug): JsonResponse
    {
        $blog = Blog::where('slug', $slug)->where('status', 'published')->first();

        if (!$blog) {
            return $this->notFoundResponse([], 'Blog not found');
        }

        return $this->okResponse(['blog' => new BlogResource($blog)], __('Blog retrieved successfully'));
    }
}
