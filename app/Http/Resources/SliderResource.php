<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_path' => $this->image_path ? url(Storage::url($this->image_path)) : null,
            'link' => $this->link,
            'alt_text' => $this->alt_text,
            'order' => $this->order,
            'status' => $this->status,
            'custom_styles' => $this->custom_styles,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
