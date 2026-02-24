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
        'app_name',
        'sitemap_enabled',
        'frontend_url',
        'blog_prefix',
        'news_prefix',
        'page_prefix',
        'case_study_prefix',
        'product_prefix',
        'ga4_property_id',
        'ga4_credentials_file',
    ];

    protected $casts = [
        'organization' => 'array',
    ];
}
