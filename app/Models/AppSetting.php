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
        // Redis configuration
        'redis_host',
        'redis_port',
        'redis_password',
        'redis_db',
        'redis_cache_db',
        'redis_session_db',
        'redis_queue_db',
        'redis_prefix',
        'redis_cache_enabled',
        'redis_session_enabled',
        'redis_queue_enabled',
        'redis_cache_ttl',
        'redis_session_ttl',
        'redis_client',
        // International SEO settings
        'default_region',
        // Email settings
        'contact_internal_enabled',
        'contact_internal_recipients',
        'contact_internal_subject',
        'contact_internal_template',
        'contact_external_enabled',
        'contact_external_subject',
        'contact_external_template',
    ];

    protected $casts = [
        'organization' => 'array',
        'contact_internal_recipients' => 'array',
        'contact_internal_enabled' => 'boolean',
        'contact_external_enabled' => 'boolean',
    ];
}
