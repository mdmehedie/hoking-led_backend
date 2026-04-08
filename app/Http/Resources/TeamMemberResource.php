<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $locale),
            'slug' => $this->slug,
            'position' => $this->getTranslation('position', $locale),
            'bio' => $this->getTranslation('bio', $locale),
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo ? url(Storage::url($this->photo)) : null,
            'social_links' => $this->social_links ?? [],
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    protected function getTranslation(string $attribute, string $locale): ?string
    {
        if (method_exists($this, 'translations')) {
            $translation = $this->translations
                ->where('attribute', $attribute)
                ->where('locale', $locale)
                ->first();

            if ($translation) {
                return $translation->value;
            }
        }

        return $this->{$attribute};
    }
}
