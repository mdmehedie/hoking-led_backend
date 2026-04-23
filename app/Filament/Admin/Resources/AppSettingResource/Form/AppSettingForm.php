<?php

namespace App\Filament\Admin\Resources\AppSettingResource\Form;

use App\Models\Locale;
use App\Models\Region;
use App\Filament\Forms\Components\TinyEditor;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;

class AppSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return $schema->schema([
            Tabs::make('App Settings Tabs')->tabs([
                Tab::make(__('Branding'))->schema([
                    Section::make(__('Logos'))->schema([
                        FileUpload::make('logo_light')
                            ->label(__('Light Logo'))
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('settings')
                            ->acceptedFileTypes(['image/*'])
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                        FileUpload::make('logo_dark')
                            ->label(__('Dark Logo'))
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('settings')
                            ->acceptedFileTypes(['image/*'])
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                    ]),
                    Section::make(__('Favicon'))->schema([
                        FileUpload::make('favicon')
                            ->label(__('Favicon'))
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('settings')
                            ->acceptedFileTypes(['image/*'])
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1']),
                    ]),
                ]),

                Tab::make(__('General Settings'))->schema([
                    Tabs::make('Language tabs')->tabs(
                        collect($activeLocales)->map(function (string $locale) use ($defaultLocale) {
                            $isDefault = $locale === $defaultLocale;

                            return Tab::make(strtoupper($locale))->schema([
                                TextInput::make("app_name.{$locale}")
                                    ->label(__('App Name'))
                                    ->required($isDefault),
                                TextInput::make("company_name.{$locale}")
                                    ->label(__('Company Name'))
                                    ->required($isDefault),
                                TinyEditor::make("about.{$locale}")
                                    ->label(__('About Company')),
                            ]);
                        })->all()
                    ),
                    Section::make(__('Appearance'))->schema([
                        ColorPicker::make('primary_color')->label(__('Primary Color'))->default('#2563eb'),
                        ColorPicker::make('secondary_color')->label(__('Secondary Color'))->default('#1e293b'),
                        ColorPicker::make('accent_color')->label(__('Accent Color'))->default('#059669'),
                        Select::make('font_family')
                            ->label(__('Font Family'))
                            ->options([
                                'Inter' => 'Inter',
                                'Roboto' => 'Roboto',
                                'Open Sans' => 'Open Sans',
                                'Lato' => 'Lato',
                                'Montserrat' => 'Montserrat',
                            ])
                            ->default('Inter'),
                        Select::make('base_font_size')
                            ->label(__('Base Font Size'))
                            ->options([
                                '12px' => '12px',
                                '14px' => '14px',
                                '16px' => '16px',
                                '18px' => '18px',
                            ])
                            ->default('16px'),
                    ])->columns(3),
                ]),

                Tab::make(__('SEO Settings'))->schema([
                    Section::make(__('Global SEO'))->schema([
                        Toggle::make('sitemap_enabled')->label(__('Enable Sitemap'))->default(true),
                        TextInput::make('frontend_url')->label(__('Frontend URL'))->url()->placeholder('https://example.com'),
                        Select::make('default_region')
                            ->label(__('Default Region'))
                            ->options(Region::pluck('name', 'code'))
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                    Section::make(__('URL Prefixes'))->schema([
                        TextInput::make('blog_prefix')->label(__('Blog Prefix'))->default('blog'),
                        TextInput::make('news_prefix')->label(__('News Prefix'))->default('news'),
                        TextInput::make('page_prefix')->label(__('Page Prefix'))->default('pages'),
                        TextInput::make('case_study_prefix')->label(__('Case Study Prefix'))->default('cases'),
                        TextInput::make('product_prefix')->label(__('Product Prefix'))->default('products'),
                    ])->columns(2),

                    Section::make(__('Google Analytics'))->schema([
                        TextInput::make('ga4_property_id')->label(__('GA4 Property ID')),
                        FileUpload::make('ga4_credentials_file')
                            ->label(__('GA4 Credentials (JSON)'))
                            ->acceptedFileTypes(['application/json'])
                            ->directory('analytics'),
                    ])->columns(2),

                    Section::make(__('Robots.txt'))->schema([
                        Toggle::make('use_default_robots_txt')->label(__('Use Default robots.txt'))->default(true)->live(),
                        CodeEditor::make('robots_txt_content')
                            ->label(__('Robots.txt Content'))
                            ->language(Language::Markdown)
                            ->visible(fn (Get $get) => !$get('use_default_robots_txt'))
                            ->columnSpanFull(),
                    ]),
                ]),

                Tab::make(__('PWA Icons'))->schema([
                    Section::make('PWA Icons')->description('Upload icons for different device sizes (PNG format recommended)')->schema([
                        FileUpload::make('pwa_icon_72')
                            ->label('Icon 72x72')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('pwa-icons')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(512)
                            ->imageResizeTargetWidth(72)
                            ->imageResizeTargetHeight(72)
                            ->helperText('72x72px icon for iPad (non-retina)'),
                        FileUpload::make('pwa_icon_96')
                            ->label('Icon 96x96')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('pwa-icons')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(512)
                            ->imageResizeTargetWidth(96)
                            ->imageResizeTargetHeight(96)
                            ->helperText('96x96px icon for various devices'),
                        FileUpload::make('pwa_icon_128')
                            ->label('Icon 128x128')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('pwa-icons')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(512)
                            ->imageResizeTargetWidth(128)
                            ->imageResizeTargetHeight(128)
                            ->helperText('128x128px icon for various devices'),
                        FileUpload::make('pwa_icon_144')
                            ->label('Icon 144x144')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('pwa-icons')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(512)
                            ->imageResizeTargetWidth(144)
                            ->imageResizeTargetHeight(144)
                            ->helperText('144x144px icon for Android Chrome'),
                        FileUpload::make('pwa_icon_192')
                            ->label('Icon 192x192')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('pwa-icons')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(512)
                            ->imageResizeTargetWidth(192)
                            ->imageResizeTargetHeight(192)
                            ->helperText('192x192px icon for Android Chrome splash screen'),
                        FileUpload::make('pwa_icon_512')
                            ->label('Icon 512x512')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('pwa-icons')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(1024)
                            ->imageResizeTargetWidth(512)
                            ->imageResizeTargetHeight(512)
                            ->helperText('512x512px icon for high-resolution displays and app stores'),
                    ])->columns(3),
                ]),

                Tab::make(__('PWA Settings'))->schema([
                    Section::make('Progressive Web App (PWA)')->description('Configure PWA settings for mobile app-like experience')->schema([
                        Toggle::make('pwa_enabled')
                            ->label('Enable PWA')
                            ->default(false)
                            ->live(),
                        TextInput::make('pwa_short_name')
                            ->label('Short Name')
                            ->placeholder('e.g. My App')
                            ->helperText('Maximum 12 characters recommended')
                            ->maxLength(12),
                        TextInput::make('pwa_description')
                            ->label('PWA Description')
                            ->placeholder('Briefly describe your application')
                            ->columnSpan(2),
                        Select::make('pwa_display_mode')
                            ->label('Display Mode')
                            ->options([
                                'fullscreen' => 'Full Screen',
                                'standalone' => 'Standalone',
                                'minimal-ui' => 'Minimal UI',
                                'browser' => 'Browser',
                            ])
                            ->default('standalone'),
                        Select::make('pwa_orientation')
                            ->label('Orientation')
                            ->options([
                                'any' => 'Any',
                                'natural' => 'Natural',
                                'portrait' => 'Portrait',
                                'landscape' => 'Landscape',
                            ])
                            ->default('any'),
                        ColorPicker::make('pwa_theme_color')
                            ->label('Theme Color')
                            ->default('#ffffff'),
                        ColorPicker::make('pwa_background_color')
                            ->label('Background Color')
                            ->default('#ffffff'),
                        TextInput::make('pwa_start_url')
                            ->label('Start URL')
                            ->default('/')
                            ->placeholder('e.g. /index.html'),
                        TextInput::make('pwa_scope')
                            ->label('Scope')
                            ->default('/')
                            ->placeholder('e.g. /'),
                        Select::make('pwa_lang')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'es' => 'Spanish',
                                'fr' => 'French',
                                'de' => 'German',
                                'zh' => 'Chinese',
                            ])
                            ->default('en'),
                        Select::make('pwa_dir')
                            ->label('Direction')
                            ->options([
                                'ltr' => 'Left to Right',
                                'rtl' => 'Right to Left',
                                'auto' => 'Auto',
                            ])
                            ->default('ltr'),
                    ])->columns(3),
                ]),

                Tab::make(__('Redis Config'))->schema([
                    Section::make(__('Redis Connection'))->schema([
                        TextInput::make('redis_host')->label('Redis Host')->default('127.0.0.1')->columnSpan(2),
                        TextInput::make('redis_port')->label('Redis Port')->default(6379)->numeric(),
                        TextInput::make('redis_password')
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
                        TextInput::make('redis_db')
                            ->label('Default DB')
                            ->numeric()
                            ->default(0)
                            ->helperText('Default Redis database number'),
                        TextInput::make('redis_cache_db')
                            ->label('Cache DB')
                            ->numeric()
                            ->default(1)
                            ->helperText('Redis database for cache'),
                        TextInput::make('redis_session_db')
                            ->label('Session DB')
                            ->numeric()
                            ->default(2)
                            ->helperText('Redis database for sessions'),
                        TextInput::make('redis_queue_db')
                            ->label('Queue DB')
                            ->numeric()
                            ->default(3)
                            ->helperText('Redis database for queue'),
                        TextInput::make('redis_prefix')
                            ->label('Key Prefix')
                            ->default('laravel_')
                            ->helperText('Prefix for all Redis keys')
                            ->columnSpan(2),
                        TextInput::make('redis_cache_ttl')
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
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
