<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CaseStudyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'category' => new CaseStudyCategoryResource($this->whenLoaded('category')),
            'bullet_fields' => $this->bullet_fields ?? [],
            'project_details' => $this->decodeProjectDetails(),
            'project_description' => $this->decodeProjectDescription(),
            'image' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'slider_images' => $this->slider_images ? array_map(
                fn ($img) => Storage::disk('public')->url($img),
                $this->slider_images
            ) : [],
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function decodeProjectDetails(): array
    {
        if (blank($this->project_details)) {
            return [];
        }

        if (is_array($this->project_details)) {
            return $this->project_details;
        }

        $decoded = json_decode($this->project_details, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    private function decodeProjectDescription(): array
    {
        $raw = $this->project_description;
        $items = [];

        if (!blank($raw)) {
            if (is_array($raw)) {
                $items = $raw;
            } else {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $items = $decoded;
                }
            }
        }

        $items = array_map(function ($item) {
            if (is_array($item)) {
                if (isset($item['image']) && !blank($item['image'])) {
                    $item['image'] = Storage::disk('public')->url($item['image']);
                } else {
                    $item['image'] = null;
                }
                return [
                    'title' => $item['title'] ?? null,
                    'description' => $item['description'] ?? null,
                    'image' => $item['image'],
                ];
            }
            return [
                'title' => null,
                'description' => null,
                'image' => null,
            ];
        }, $items);

        while (count($items) < 2) {
            $items[] = [
                'title' => null,
                'description' => null,
                'image' => null,
            ];
        }

        return array_slice($items, 0, 2);
    }
}
