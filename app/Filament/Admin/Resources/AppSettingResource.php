<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AppSettingResource\Pages as Pages;
use App\Models\AppSetting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class AppSettingResource extends Resource
{
    protected static ?string $model = AppSetting::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog;

    protected static ?string $navigationLabel = 'App Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Logos')->schema([
                FileUpload::make('logo_light')->image()->directory('settings')->acceptedFileTypes(['image/*']),
                FileUpload::make('logo_dark')->image()->directory('settings')->acceptedFileTypes(['image/*']),
            ]),
            Section::make('Favicon')->schema([
                FileUpload::make('favicon')->image()->directory('settings')->acceptedFileTypes(['image/*']),
            ]),
            Section::make('Brand Colors')->schema([
                ColorPicker::make('primary_color')->default('#3b82f6')->required(),
                ColorPicker::make('secondary_color')->default('#10b981')->required(),
                ColorPicker::make('accent_color')->default('#f59e0b')->required(),
            ]),
            Section::make('Typography')->schema([
                Select::make('font_family')->options([
                    'Arial' => 'Arial',
                    'Helvetica' => 'Helvetica',
                    'Times New Roman' => 'Times New Roman',
                    'Courier New' => 'Courier New',
                ])->default('Arial')->required(),
                \Filament\Forms\Components\TextInput::make('base_font_size')->default('16px')->required(),
            ]),
            Section::make('Organization')->schema([
                \Filament\Forms\Components\TextInput::make('organization.company_name')
                    ->label('Company name')
                    ->default(''),
                \Filament\Forms\Components\TextInput::make('app_name')
                    ->label('Company Title')
                    ->default('Admin Panel'),
                \Filament\Forms\Components\TextInput::make('frontend_url')
                    ->label('Frontend URL')
                    ->url()
                    ->placeholder('https://your-frontend-domain.com')
                    ->helperText('The URL of your frontend website. Used for generating share links in social media posts.')
                    ->default(''),
                \App\Filament\Forms\Components\CustomRichEditor::make('organization.about')
                    ->label('About information')
                    ->default(''),
                Repeater::make('organization.contact_emails')
                    ->label('Contact email(s)')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('email')->email()->required(),
                    ])
                    ->default([]),
                Repeater::make('organization.contact_phones')
                    ->label('Contact phone number(s)')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('phone')->required(),
                    ])
                    ->default([]),
                Repeater::make('organization.office_addresses')
                    ->label('Office addresses')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('label')->required(),
                        \Filament\Forms\Components\Textarea::make('street')->rows(2)->required(),
                        \Filament\Forms\Components\TextInput::make('city')->required(),
                        \Filament\Forms\Components\TextInput::make('country')->required(),
                        \Filament\Forms\Components\TextInput::make('map_link')->label('Map link (Google Maps URL)')->url(),
                    ])
                    ->default([]),
                Repeater::make('organization.social_links')
                    ->label('Social media profile links')
                    ->schema([
                        Select::make('platform')
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
                            ->label('URL')
                            ->required(),
                    ])
                    ->default([]),
            ]),
            Section::make('Toastr Settings')->schema([
                Toggle::make('toastr_enabled')->label('Enable Toastr Notifications')->default(true),
                Select::make('toastr_position')->label('Position')->options([
                    'top-left' => 'Top Left',
                    'top-right' => 'Top Right',
                    'bottom-left' => 'Bottom Left',
                    'bottom-right' => 'Bottom Right',
                ])->default('top-right'),
                \Filament\Forms\Components\TextInput::make('toastr_duration')->label('Duration (ms)')->numeric()->default(5000),
                Select::make('toastr_show_method')->label('Show Method')->options([
                    'fadeIn' => 'Fade In',
                    'slideDown' => 'Slide Down',
                ])->default('fadeIn'),
                Select::make('toastr_hide_method')->label('Hide Method')->options([
                    'fadeOut' => 'Fade Out',
                    'slideUp' => 'Slide Up',
                ])->default('fadeOut'),
            ]),
            Section::make('SEO Settings')->schema([
                Toggle::make('sitemap_enabled')->label('Enable Sitemap Generation')->default(true),
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
