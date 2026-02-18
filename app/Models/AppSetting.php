<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'logo_light',
        'logo_dark',
        'favicon',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'base_font_size',
        'organization',
        'toastr_enabled',
        'toastr_position',
        'toastr_duration',
        'toastr_show_method',
        'toastr_hide_method',
    ];

    protected $casts = [
        'organization' => 'array',
    ];
}
