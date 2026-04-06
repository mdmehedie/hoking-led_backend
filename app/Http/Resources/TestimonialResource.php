<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'client_position' => $this->client_position,
            'client_company' => $this->client_company,
            'testimonial' => $this->testimonial,
            'rating' => $this->rating,
            'image' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'sort_order' => $this->sort_order,
        ];
    }
}
