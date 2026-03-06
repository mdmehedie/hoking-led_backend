<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AppSettingResource\Pages as Pages;
use App\Models\AppSetting;
use App\Models\Locale;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class AppSettingResource extends Resource
{
    protected static ?string $model = AppSetting::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog;

    protected static ?string $navigationLabel = 'App Settings';

    public static function getNavigationLabel(): string
    {
        return __('App Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create appsetting');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit appsetting');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete appsetting');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }

    public static function form(Schema $schema): Schema
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return $schema->schema([
            Section::make(__('Logos'))->schema([
                FileUpload::make('logo_light')
                    ->label(__('Light Logo'))
                    ->image()
                    ->directory('settings')
                    ->acceptedFileTypes(['image/*'])
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                FileUpload::make('logo_dark')
                    ->label(__('Dark Logo'))
                    ->image()
                    ->directory('settings')
                    ->acceptedFileTypes(['image/*'])
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
            ]),
            Section::make(__('Favicon'))->schema([
                FileUpload::make('favicon')
                    ->label(__('Favicon'))
                    ->image()
                    ->directory('settings')
                    ->acceptedFileTypes(['image/*'])
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1']),
            ]),
            Section::make(__('Brand Colors'))->schema([
                ColorPicker::make('primary_color')
                    ->label(__('Primary Color'))
                    ->default('#3b82f6')
                    ->required(),
                ColorPicker::make('secondary_color')
                    ->label(__('Secondary Color'))
                    ->default('#10b981')
                    ->required(),
                ColorPicker::make('accent_color')
                    ->label(__('Accent Color'))
                    ->default('#f59e0b')
                    ->required(),
            ]),
            Section::make(__('Typography'))->schema([
                Select::make('font_family')
                    ->label(__('Font Family'))
                    ->options([
                        'Arial' => 'Arial',
                        'Helvetica' => 'Helvetica',
                        'Times New Roman' => 'Times New Roman',
                        'Courier New' => 'Courier New',
                    ])
                    ->default('Arial')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('base_font_size')
                    ->label(__('Base Font Size'))
                    ->default('16px')
                    ->required(),
            ]),
            Section::make(__('Organization'))->schema([
                Tabs::make('Translations')->tabs(
                    collect($activeLocales)->map(function (string $locale) use ($defaultLocale) {
                        $isDefault = $locale === $defaultLocale;

                        return Tab::make(strtoupper($locale))
                            ->schema([
                                \Filament\Forms\Components\TextInput::make("app_name.{$locale}")
                                    ->label(__('Company Title'))
                                    ->default('Admin Panel')
                                    ->required($isDefault),
                                \Filament\Forms\Components\TextInput::make("company_name.{$locale}")
                                    ->label(__('Company name'))
                                    ->default('')
                                    ->required($isDefault),
                                \App\Filament\Forms\Components\CustomRichEditor::make("about.{$locale}")
                                    ->label(__('About information'))
                                    ->default('')
                                    ->required($isDefault),
                            ]);
                    })->all()
                ),
                \Filament\Forms\Components\TextInput::make('frontend_url')
                    ->label(__('Frontend URL'))
                    ->url()
                    ->placeholder('https://your-frontend-domain.com')
                    ->helperText(__('The URL of your frontend website. Used for generating share links in social media posts.'))
                    ->default(''),
                Repeater::make('organization.contact_emails')
                    ->label(__('Contact email(s)'))
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required(),
                    ])
                    ->default([]),
                Repeater::make('organization.contact_phones')
                    ->label(__('Contact phone number(s)'))
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->label(__('Phone Number'))
                            ->required(),
                    ])
                    ->default([]),
                Repeater::make('organization.office_addresses')
                    ->label(__('Office addresses'))
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('label')
                            ->label(__('Label'))
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('street')
                            ->label(__('Street'))
                            ->rows(2)
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('city')
                            ->label(__('City'))
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('country')
                            ->label(__('Country'))
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('map_link')
                            ->label(__('Map link (Google Maps URL)'))
                            ->url(),
                    ])
                    ->default([]),
                Repeater::make('organization.social_links')
                    ->label(__('Social media profile links'))
                    ->schema([
                        Select::make('platform')
                            ->label(__('Platform'))
                            ->options([
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter / X',
                                'linkedin' => 'LinkedIn',
                                'instagram' => 'Instagram',
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                                'github' => 'GitHub',
                                'website' => 'Website',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('url')
                            ->label(__('URL'))
                            ->required(),
                    ])
                    ->default([]),
            ]),
            Section::make(__('Toastr Settings'))->schema([
                Toggle::make('toastr_enabled')
                    ->label(__('Enable Toastr Notifications'))
                    ->default(true),
                Select::make('toastr_position')
                    ->label(__('Position'))
                    ->options([
                        'top-left' => __('Top Left'),
                        'top-right' => __('Top Right'),
                        'bottom-left' => __('Bottom Left'),
                        'bottom-right' => __('Bottom Right'),
                    ])
                    ->default('top-right'),
                \Filament\Forms\Components\TextInput::make('toastr_duration')
                    ->label(__('Duration (ms)'))
                    ->numeric()
                    ->default(5000),
                Select::make('toastr_show_method')
                    ->label(__('Show Method'))
                    ->options([
                        'fadeIn' => __('Fade In'),
                        'slideDown' => __('Slide Down'),
                    ])
                    ->default('fadeIn'),
                Select::make('toastr_hide_method')
                    ->label(__('Hide Method'))
                    ->options([
                        'fadeOut' => __('Fade Out'),
                        'slideUp' => __('Slide Up'),
                    ])
                    ->default('fadeOut'),
            ]),
            Section::make(__('SEO Settings'))->schema([
                Toggle::make('sitemap_enabled')
                    ->label(__('Enable Sitemap Generation'))
                    ->default(true),
            ]),
            Section::make(__('Robots.txt Settings'))
                ->description(__('Manage robots.txt content for search engine crawlers'))
                ->schema([
                Toggle::make('use_default_robots_txt')
                    ->label(__('Use Default Robots.txt'))
                    ->default(true)
                    ->helperText(__('When enabled, uses a default robots.txt that allows all crawlers. When disabled, uses custom content below.'))
                    ->reactive(),
                \Filament\Forms\Components\Textarea::make('robots_txt_content')
                    ->label(__('Custom Robots.txt Content'))
                    ->rows(10)
                    ->placeholder('User-agent: *
Disallow: /admin/
Disallow: /storage/private/
Allow: /

Sitemap: https://your-domain.com/sitemap.xml')
                    ->helperText('Enter custom robots.txt content. This will be used when "Use Default Robots.txt" is disabled. Make sure to follow proper robots.txt syntax.')
                    ->disabled(fn ($get) => $get('use_default_robots_txt'))
                    ->required(fn ($get) => !$get('use_default_robots_txt'))
                    ->dehydrated(true),
            ]),
            Section::make('URL Prefixes')->description('Configure URL prefixes for different content types used in social media sharing')->schema([
                \Filament\Forms\Components\TextInput::make('blog_prefix')
                    ->label('Blog URL Prefix')
                    ->placeholder('/blog/')
                    ->default('/blog/')
                    ->helperText('URL prefix for blog posts (e.g., /blog/ or /articles/)'),
                \Filament\Forms\Components\TextInput::make('news_prefix')
                    ->label('News URL Prefix')
                    ->placeholder('/news/')
                    ->default('/news/')
                    ->helperText('URL prefix for news articles'),
                \Filament\Forms\Components\TextInput::make('page_prefix')
                    ->label('Page URL Prefix')
                    ->placeholder('/pages/')
                    ->default('/pages/')
                    ->helperText('URL prefix for static pages'),
                \Filament\Forms\Components\TextInput::make('case_study_prefix')
                    ->label('Case Study URL Prefix')
                    ->placeholder('/case-studies/')
                    ->default('/case-studies/')
                    ->helperText('URL prefix for case studies'),
                \Filament\Forms\Components\TextInput::make('product_prefix')
                    ->label('Product URL Prefix')
                    ->placeholder('/products/')
                    ->default('/products/')
                    ->helperText('URL prefix for products'),
            ]),
            Section::make('Google Analytics 4')->description('Configure Google Analytics 4 integration for dashboard metrics')->schema([
                \Filament\Forms\Components\TextInput::make('ga4_property_id')
                    ->label('GA4 Property ID')
                    ->placeholder('123456789')
                    ->helperText('Your Google Analytics 4 property ID (found in GA4 admin)'),
                \Filament\Forms\Components\FileUpload::make('ga4_credentials_file')
                    ->label('GA4 Credentials JSON')
                    ->acceptedFileTypes(['application/json'])
                    ->maxSize(2048)
                    ->helperText('Upload your GA4 service account credentials JSON file'),
            ]),
            Section::make('Progressive Web App (PWA)')->description('Configure PWA settings for mobile app-like experience')->schema([
                Toggle::make('pwa_enabled')
                    ->label('Enable PWA')
                    ->default(false)
                    ->helperText('Enable progressive web app features'),
                \Filament\Forms\Components\TextInput::make('pwa_short_name')
                    ->label('PWA Short Name')
                    ->placeholder('App')
                    ->helperText('Short name displayed on home screen (max 12 characters)')
                    ->maxLength(12),
                \Filament\Forms\Components\Textarea::make('pwa_description')
                    ->label('PWA Description')
                    ->placeholder('Progressive Web App description')
                    ->helperText('Description of your PWA app')
                    ->rows(2),
                Select::make('pwa_display_mode')
                    ->label('Display Mode')
                    ->options([
                        'standalone' => 'Standalone (App-like)',
                        'fullscreen' => 'Fullscreen',
                        'minimal-ui' => 'Minimal UI',
                        'browser' => 'Browser (No PWA)',
                    ])
                    ->default('standalone')
                    ->helperText('How the app should be displayed when launched'),
                Select::make('pwa_orientation')
                    ->label('Orientation')
                    ->options([
                        'portrait-primary' => 'Portrait Primary',
                        'landscape-primary' => 'Landscape Primary',
                        'portrait' => 'Portrait (Any)',
                        'landscape' => 'Landscape (Any)',
                        'any' => 'Any Orientation',
                    ])
                    ->default('portrait-primary')
                    ->helperText('Preferred orientation for the PWA'),
                ColorPicker::make('pwa_theme_color')
                    ->label('Theme Color')
                    ->helperText('Color for browser UI elements (address bar, etc.)'),
                ColorPicker::make('pwa_background_color')
                    ->label('Background Color')
                    ->helperText('Background color shown during app launch'),
                \Filament\Forms\Components\TextInput::make('pwa_start_url')
                    ->label('Start URL')
                    ->placeholder('/')
                    ->default('/')
                    ->helperText('URL to load when PWA is launched'),
                \Filament\Forms\Components\TextInput::make('pwa_scope')
                    ->label('Scope')
                    ->placeholder('/')
                    ->default('/')
                    ->helperText('URL scope for the PWA (controls which pages can be accessed)'),
                Select::make('pwa_lang')
                    ->label('Language')
                    ->options([
                        'en-US' => 'English (US)',
                        'en-GB' => 'English (UK)',
                        'es-ES' => 'Spanish',
                        'fr-FR' => 'French',
                        'de-DE' => 'German',
                        'it-IT' => 'Italian',
                        'pt-BR' => 'Portuguese (Brazil)',
                        'zh-CN' => 'Chinese (Simplified)',
                        'ja-JP' => 'Japanese',
                        'ko-KR' => 'Korean',
                    ])
                    ->default('en-US')
                    ->helperText('Primary language of the PWA'),
                Select::make('pwa_dir')
                    ->label('Text Direction')
                    ->options([
                        'ltr' => 'Left to Right',
                        'rtl' => 'Right to Left',
                    ])
                    ->default('ltr')
                    ->helperText('Text direction for the PWA'),
            ]),
            Section::make('PWA Icons')->description('Upload icons for different device sizes (PNG format recommended)')->schema([
                FileUpload::make('pwa_icon_72')
                    ->label('Icon 72x72')
                    ->image()
                    ->directory('pwa-icons')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(512)
                    ->imageResizeTargetWidth(72)
                    ->imageResizeTargetHeight(72)
                    ->helperText('72x72px icon for iPad (non-retina)'),
                FileUpload::make('pwa_icon_96')
                    ->label('Icon 96x96')
                    ->image()
                    ->directory('pwa-icons')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(512)
                    ->imageResizeTargetWidth(96)
                    ->imageResizeTargetHeight(96)
                    ->helperText('96x96px icon for various devices'),
                FileUpload::make('pwa_icon_128')
                    ->label('Icon 128x128')
                    ->image()
                    ->directory('pwa-icons')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(512)
                    ->imageResizeTargetWidth(128)
                    ->imageResizeTargetHeight(128)
                    ->helperText('128x128px icon for various devices'),
                FileUpload::make('pwa_icon_144')
                    ->label('Icon 144x144')
                    ->image()
                    ->directory('pwa-icons')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(512)
                    ->imageResizeTargetWidth(144)
                    ->imageResizeTargetHeight(144)
                    ->helperText('144x144px icon for Android Chrome'),
                FileUpload::make('pwa_icon_192')
                    ->label('Icon 192x192')
                    ->image()
                    ->directory('pwa-icons')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(512)
                    ->imageResizeTargetWidth(192)
                    ->imageResizeTargetHeight(192)
                    ->helperText('192x192px icon for Android Chrome splash screen'),
                FileUpload::make('pwa_icon_512')
                    ->label('Icon 512x512')
                    ->image()
                    ->directory('pwa-icons')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(1024)
                    ->imageResizeTargetWidth(512)
                    ->imageResizeTargetHeight(512)
                    ->helperText('512x512px icon for high-resolution displays and app stores'),
            ]),
            Section::make('Redis Configuration')->description('Configure Redis server settings for caching, sessions, and queue management')->schema([
                \Filament\Forms\Components\TextInput::make('redis_host')
                    ->label('Redis Host')
                    ->default('127.0.0.1')
                    ->helperText('Redis server hostname or IP address')
                    ->columnSpan(2),
                \Filament\Forms\Components\TextInput::make('redis_port')
                    ->label('Redis Port')
                    ->numeric()
                    ->default(6379)
                    ->helperText('Redis server port number')
                    ->columnSpan(2),
                \Filament\Forms\Components\TextInput::make('redis_password')
                    ->label('Redis Password')
                    ->password()
                    ->nullable()
                    ->helperText('Leave empty if no password is required')
                    ->columnSpan(2),
                Select::make('redis_client')
                    ->label('Redis Client')
                    ->options([
                        'phpredis' => 'PHPRedis (PHP Extension)',
                        'predis' => 'Predis (Pure PHP)',
                    ])
                    ->default('phpredis')
                    ->helperText('Choose Redis client implementation')
                    ->columnSpan(2),
                \Filament\Forms\Components\TextInput::make('redis_db')
                    ->label('Default DB')
                    ->numeric()
                    ->default(0)
                    ->helperText('Default Redis database number'),
                \Filament\Forms\Components\TextInput::make('redis_cache_db')
                    ->label('Cache DB')
                    ->numeric()
                    ->default(1)
                    ->helperText('Redis database for cache'),
                \Filament\Forms\Components\TextInput::make('redis_session_db')
                    ->label('Session DB')
                    ->numeric()
                    ->default(2)
                    ->helperText('Redis database for sessions'),
                \Filament\Forms\Components\TextInput::make('redis_queue_db')
                    ->label('Queue DB')
                    ->numeric()
                    ->default(3)
                    ->helperText('Redis database for queue'),
                \Filament\Forms\Components\TextInput::make('redis_prefix')
                    ->label('Key Prefix')
                    ->default('laravel_')
                    ->helperText('Prefix for all Redis keys')
                    ->columnSpan(2),
                \Filament\Forms\Components\TextInput::make('redis_cache_ttl')
                    ->label('Cache TTL (seconds)')
                    ->numeric()
                    ->default(3600)
                    ->helperText('Default time-to-live for cache items')
                    ->columnSpan(2),
                Toggle::make('redis_cache_enabled')
                    ->label('Enable Cache')
                    ->default(true)
                    ->helperText('Enable Redis for application caching'),
                Toggle::make('redis_session_enabled')
                    ->label('Enable Sessions')
                    ->default(true)
                    ->helperText('Enable Redis for session storage'),
                Toggle::make('redis_queue_enabled')
                    ->label('Enable Queue')
                    ->default(true)
                    ->helperText('Enable Redis for queue management'),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('primary_color'),
            Tables\Columns\TextColumn::make('font_family'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppSettings::route('/'),
            'create' => Pages\CreateAppSetting::route('/create'),
            'edit' => Pages\EditAppSetting::route('/{record}/edit'),
        ];
    }
}
