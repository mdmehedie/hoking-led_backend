<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $content = $this->content;

        if ($this->slug === 'company' && is_array($content)) {
            $content = $this->transformCompanyContent($content);
        }

        if ($this->slug === 'about-us' && is_array($content)) {
            $content = $this->transformAboutUsContent($content);
        }

        if ($this->slug === 'after-sale-service' && is_array($content)) {
            $content = $this->transformAfterSaleServiceContent($content);
        }

        if ($this->slug === 'contact' && is_array($content)) {
            $content = $this->transformContactContent($content);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $content,
            'image' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'author' => $this->when($this->author, fn () => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ]),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Transform relative paths to absolute URLs for the company page.
     */
    protected function transformCompanyContent(array $content): array
    {
        $imageFields = ['hero_bg', 'banner', 'bottom_image'];
        foreach ($imageFields as $field) {
            if (!empty($content[$field])) {
                $content[$field] = Storage::disk('public')->url($content[$field]);
            }
        }

        if (!empty($content['our_factory'])) {
            $factoryImages = ['image_1', 'image_2', 'image_3'];
            foreach ($factoryImages as $img) {
                if (!empty($content['our_factory'][$img])) {
                    $content['our_factory'][$img] = Storage::disk('public')->url($content['our_factory'][$img]);
                }
            }
        }

        return $content;
    }

    /**
     * Transform relative paths to absolute URLs for the about-us page.
     */
    protected function transformAboutUsContent(array $content): array
    {
        $imageFields = ['image_1', 'image_2', 'mission_vision_image'];
        foreach ($imageFields as $field) {
            if (!empty($content[$field])) {
                $content[$field] = Storage::disk('public')->url($content[$field]);
            }
        }

        if (!empty($content['mission']['icon'])) {
            $content['mission']['icon'] = Storage::disk('public')->url($content['mission']['icon']);
        }

        if (!empty($content['vision']['icon'])) {
            $content['vision']['icon'] = Storage::disk('public')->url($content['vision']['icon']);
        }

        return $content;
    }

    /**
     * Transform relative paths to absolute URLs for the after-sale-service page.
     */
    protected function transformAfterSaleServiceContent(array $content): array
    {
        if (!empty($content['hero_bg'])) {
            $content['hero_bg'] = Storage::disk('public')->url($content['hero_bg']);
        }

        if (!empty($content['services']) && is_array($content['services'])) {
            foreach ($content['services'] as &$service) {
                if (!empty($service['icon'])) {
                    $service['icon'] = Storage::disk('public')->url($service['icon']);
                }
            }
        }

        return $content;
    }

    /**
     * Transform relative paths to absolute URLs for the contact page.
     */
    protected function transformContactContent(array $content): array
    {
        if (!empty($content['contacts']) && is_array($content['contacts'])) {
            foreach ($content['contacts'] as &$contact) {
                if (!empty($contact['icon'])) {
                    $contact['icon'] = Storage::disk('public')->url($contact['icon']);
                }
            }
        }

        return $content;
    }
}
