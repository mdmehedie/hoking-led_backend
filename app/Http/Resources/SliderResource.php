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
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'translations' => $this->translations,
            'media_type' => $this->media_type,
            'link' => $this->link,
            'alt_text' => $this->alt_text,
            'order' => $this->order,
            'status' => $this->status,
            'custom_styles' => $this->custom_styles,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add the relevant media field based on type
        switch ($this->media_type) {
            case 'image':
            case 'gif':
                $data['image_path'] = $this->image_path ? url(Storage::url($this->image_path)) : null;
                break;
            case 'video_url':
                $data['video_url'] = $this->video_url;
                break;
            case 'video_file':
                $data['video_file'] = $this->video_file ? url(Storage::url($this->video_file)) : null;
                break;
        }

        return $data;
    }
}
