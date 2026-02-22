<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AppSettingResource extends JsonResource
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
            'logo_light' => $this->logo_light ? url(Storage::url($this->logo_light)) : null,
            'logo_dark' => $this->logo_dark ? url(Storage::url($this->logo_dark)) : null,
            'favicon' => $this->favicon ? url(Storage::url($this->favicon)) : null,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'accent_color' => $this->accent_color,
            'font_family' => $this->font_family,
            'base_font_size' => $this->base_font_size,
            'organization' => $this->organization,
            'company_name' => $this->organization['company_name'] ?? null,
            'about' => $this->organization['about'] ?? null,
            'contact_emails' => $this->organization['contact_emails'] ?? [],
            'contact_phones' => $this->organization['contact_phones'] ?? [],
            'office_addresses' => $this->organization['office_addresses'] ?? [],
            'social_links' => $this->organization['social_links'] ?? [],
            'toastr_enabled' => $this->toastr_enabled,
            'toastr_position' => $this->toastr_position,
            'toastr_duration' => $this->toastr_duration,
            'toastr_show_method' => $this->toastr_show_method,
            'toastr_hide_method' => $this->toastr_hide_method,
            'app_name' => $this->app_name,
        ];
    }
}
