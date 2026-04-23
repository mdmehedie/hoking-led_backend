<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LocaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'direction' => $this->direction,
            'is_default' => (bool) $this->is_default,
            'flag_url' => $this->flag_path ? Storage::disk('public')->url($this->flag_path) : null,
        ];
    }
}
