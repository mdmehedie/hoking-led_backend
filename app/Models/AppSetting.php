<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslations;

class AppSetting extends Model
{
    use HasTranslations;

    protected array $translatable = [
        'app_name',
        'company_name',
        'about',
    ];

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
        'company_name',
        'about',
        'sitemap_enabled',
        'frontend_url',
        'blog_prefix',
        'news_prefix',
        'page_prefix',
        'case_study_prefix',
        'product_prefix',
        'ga4_property_id',
        'ga4_credentials_file',
        // PWA fields
        'pwa_enabled',
        'pwa_display_mode',
        'pwa_orientation',
        'pwa_theme_color',
        'pwa_background_color',
        'pwa_icon_72',
        'pwa_icon_96',
        'pwa_icon_128',
        'pwa_icon_144',
        'pwa_icon_192',
        'pwa_icon_512',
        'pwa_short_name',
        'pwa_description',
        'pwa_categories',
        'pwa_start_url',
        'pwa_scope',
        'pwa_lang',
        'pwa_dir',
        // Robots.txt settings
        'robots_txt_content',
        'use_default_robots_txt',
    ];

    protected $casts = [
        'organization' => 'array',
    ];
}
