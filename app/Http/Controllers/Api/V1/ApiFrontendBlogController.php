<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;

class ApiFrontendBlogController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $blogs = Blog::where('status', 'published')->orderBy('published_at', 'desc')->get();

        return $this->okResponse(['blogs' => BlogResource::collection($blogs)], __('Blogs retrieved successfully'));
    }
}
