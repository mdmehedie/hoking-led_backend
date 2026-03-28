<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TestimonialCollection extends ResourceCollection
{
    public $collects = TestimonialResource::class;

    public function toArray(Request $request): array
    {
        return [
            'current_page' => $this->resource->currentPage(),
            'data' => $this->collection,
            'first_page_url' => $this->resource->url(1),
            'from' => $this->resource->firstItem(),
            'last_page' => $this->resource->lastPage(),
            'last_page_url' => $this->resource->url($this->resource->lastPage()),
            'links' => collect($this->resource->linkCollection()->toArray())->map(function ($link) {
                return [
                    'url' => $link['url'] ?? null,
                    'label' => $link['label'] ?? null,
                    'page' => $link['page'] ?? null,
                    'active' => $link['active'] ?? false,
                ];
            })->toArray(),
            'next_page_url' => $this->resource->nextPageUrl(),
            'path' => $request->url(),
            'per_page' => $this->resource->perPage(),
            'prev_page_url' => $this->resource->previousPageUrl(),
            'to' => $this->resource->lastItem(),
            'total' => $this->resource->total(),
        ];
    }
}
