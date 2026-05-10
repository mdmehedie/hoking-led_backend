<?php

namespace App\Filament\Admin\Resources\PageResource\Form;

use App\Filament\Forms\Components\TinyEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;

class PageForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Page Tabs')->tabs([
                Tab::make(__('General Information'))->schema(self::generalSchema()),
                Tab::make(__('Page Details'))->schema(self::pageDetailsSchema()),
                Tab::make(__('SEO'))->schema(self::seoSchema()),
            ])->columnSpanFull(),
        ]);
    }

    private static function pageDetailsSchema(): array
    {
        return [
            Section::make('Page Content')->schema(function ($record) {
                $slug = $record?->slug;

                if ($slug === 'company') {
                    return self::companyPageSchema();
                }

                if ($slug === 'about-us') {
                    return self::aboutUsPageSchema();
                }

                if ($slug === 'after-sale-service') {
                    return self::afterSaleServicePageSchema();
                }

                if ($slug === 'contact') {
                    return self::contactPageSchema();
                }

                return [
                    TinyEditor::make("content")
                        ->label(__('Content'))
                        ->columnSpanFull(),
                ];
            })
        ];
    }

    private static function generalSchema(): array
    {
        return [
            TextInput::make('slug')
                ->label(__('Slug'))
                ->readOnly()
                ->required(),
            Select::make('status')
                ->label(__('Status'))
                ->options([
                    'draft' => __('Draft'),
                    'review' => __('Review'),
                    'published' => __('Published'),
                ])
                ->default('draft')
                ->required(),
            Hidden::make('author_id')
                ->default(fn () => auth()->id()),
            Hidden::make('published_at')
                ->default(fn () => now()),
        ];
    }

    private static function companyPageSchema(): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            Section::make(__('Hero Section'))->schema([
                FileUpload::make("content.hero_bg")
                    ->label(__('Hero Background'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => empty($get("content.hero_video")))
                    ->directory('pages/company')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->columnSpanFull()
                    ->live(),
                FileUpload::make("content.hero_video")
                    ->label(__('Hero Video'))
                    ->disk('public')
                    ->visibility('public')
                    ->required(fn (Get $get) => empty($get("content.hero_bg")))
                    ->directory('pages/company/videos')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                    ->maxSize(51200) // 50MB
                    ->columnSpanFull()
                    ->live(),
                TextInput::make("content.hero_title")
                    ->label(__('Hero Title'))
                    ->required()
                    ->maxLength(255),
                TextInput::make("content.hero_secondary_title")
                    ->label(__('Hero Secondary Title'))
                    ->required()
                    ->maxLength(255),
                Textarea::make("content.hero_description")
                    ->label(__('Hero Description'))
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make(__('Banner Section'))->schema([
                FileUpload::make("content.banner")
                    ->label(__('Banner Image'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required()
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->directory('pages/company')
                    ->columnSpanFull(),
            ]),

            Section::make(__('Our Company'))->schema([
                Repeater::make("content.our_company_description")
                    ->label(__('Company Description Paragraphs'))
                    ->required()
                    ->simple(
                        Textarea::make('paragraph')
                            ->required()
                    )
                    ->columnSpanFull(),
            ]),

            Section::make(__('Our Factory'))->schema([
                TextInput::make("content.our_factory.title")
                    ->label(__('Factory Title'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make("content.our_factory.description_1")
                    ->label(__('Description 1'))
                    ->required()
                    ->maxLength(1000),
                Textarea::make("content.our_factory.description_2")
                    ->label(__('Description 2'))
                    ->required()
                    ->maxLength(1000),
                TextInput::make("content.our_factory.redirect_link")
                    ->label(__('Redirect Link'))
                    ->required()
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
                FileUpload::make("content.our_factory.image_1")
                    ->label(__('Image 1'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->required()
                    ->directory('pages/company'),
                FileUpload::make("content.our_factory.image_2")
                    ->label(__('Image 2'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->required()
                    ->directory('pages/company'),
                FileUpload::make("content.our_factory.image_3")
                    ->label(__('Image 3'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->required()
                    ->directory('pages/company'),
            ])->columns(2),

            Section::make(__('Certifications Section'))->schema([
                TextInput::make("content.certification_title")
                    ->label(__('Certification Title'))
                    ->required()
                    ->maxLength(255),
                Textarea::make("content.certification_description")
                    ->label(__('Certification Description'))
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Pillars & Growth'))->schema([
                Textarea::make("content.mission")
                    ->label(__('Mission'))
                    ->required(),
                Textarea::make("content.value")
                    ->label(__('Value'))
                    ->required(),
                Textarea::make("content.growth")
                    ->label(__('Growth'))
                    ->required(),
                FileUpload::make("content.bottom_image")
                    ->label(__('Bottom Image'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required()
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->directory('pages/company')
                    ->columnSpanFull(),
            ])->columns(1),
        ];
    }

    private static function aboutUsPageSchema(): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            Section::make(__('Hero & Intro'))->schema([
                TextInput::make("content.title")
                    ->label(__('Intro Title'))
                    ->required(),
                TextInput::make("content.secondary_title")
                    ->label(__('Secondary Title'))
                    ->required(),
                Textarea::make("content.first_description")
                    ->label(__('First Description'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make("content.first_description_redirect_link")
                    ->label(__('Redirect Link'))
                    ->url()
                    ->required(),
                FileUpload::make("content.image_1")
                    ->label(__('Image 1'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required()
                    ->directory('pages/about-us')
                    ->getUploadedFileNameForStorageUsing($keepOriginal),
                FileUpload::make("content.image_2")
                    ->label(__('Image 2'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required()
                    ->directory('pages/about-us')
                    ->getUploadedFileNameForStorageUsing($keepOriginal),
            ])->columns(2),

            Section::make(__('Stats & Service'))->schema([
                TextInput::make("content.service")
                    ->label(__('Service Text'))
                    ->required(),
                TextInput::make("content.countries_active_clients")
                    ->label(__('Countries with Active Clients'))
                    ->required(),
                TextInput::make("content.years_warranty")
                    ->label(__('Years Warranty'))
                    ->required(),
                TextInput::make("content.service_warranty")
                    ->label(__('Service Warranty'))
                    ->required(),
            ])->columns(2),

            Section::make(__('Our Process'))->schema([
                TextInput::make("content.our_process.title")
                    ->label(__('Process Title'))
                    ->required()
                    ->columnSpanFull(),
                Repeater::make("content.our_process.steps")
                    ->label(__('Process Steps'))
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->required(),
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]),

            Section::make(__('Mission & Vision'))->schema([
                FileUpload::make("content.mission_vision_image")
                    ->label(__('Main Section Image'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required()
                    ->directory('pages/about-us')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->columnSpanFull(),
                TextInput::make("content.mission_vision_title")
                    ->label(__('Section Title'))
                    ->required()
                    ->columnSpanFull(),
                
                Section::make(__('Mission'))->schema([
                    TextInput::make("content.mission.title")
                        ->label(__('Mission Title'))
                        ->required(),
                    FileUpload::make("content.mission.icon")
                        ->label(__('Mission Icon'))
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->required()
                        ->directory('pages/about-us')
                        ->getUploadedFileNameForStorageUsing($keepOriginal),
                    Textarea::make("content.mission.description")
                        ->label(__('Mission Description'))
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make(__('Vision'))->schema([
                    TextInput::make("content.vision.title")
                        ->label(__('Vision Title'))
                        ->required(),
                    FileUpload::make("content.vision.icon")
                        ->label(__('Vision Icon'))
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->required()
                        ->directory('pages/about-us')
                        ->getUploadedFileNameForStorageUsing($keepOriginal),
                    Textarea::make("content.vision.description")
                        ->label(__('Vision Description'))
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),
            ]),
        ];
    }

    private static function afterSaleServicePageSchema(): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            Section::make(__('Hero Section'))->schema([
                FileUpload::make("content.hero_bg")
                    ->label(__('Hero Background'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required()
                    ->directory('pages/after-sale-service')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->columnSpanFull(),
                TextInput::make("content.hero_title")
                    ->label(__('Hero Title'))
                    ->required()
                    ->maxLength(255),
                Textarea::make("content.hero_description")
                    ->label(__('Hero Description'))
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Services'))->schema([
                Repeater::make("content.services")
                    ->label(__('Support Services'))
                    ->schema([
                        FileUpload::make('icon')
                            ->label(__('Icon'))
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->required()
                            ->directory('pages/after-sale-service')
                            ->getUploadedFileNameForStorageUsing($keepOriginal),
                        TextInput::make('title')->label(__('Title'))->required(),
                        Textarea::make('description')->label(__('Description'))->required(),
                    ])
                    ->required()
                    ->columns(1)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Warranty Policy'))->schema([
                Repeater::make("content.quality_warranty_policy")
                    ->label(__('Our Quality Warranty Policy'))
                    ->required()
                    ->simple(
                        Textarea::make('policy_text')
                            ->required()
                    )
                    ->columnSpanFull(),
            ]),
        ];
    }

    private static function contactPageSchema(): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            Section::make(__('Header'))->schema([
                FileUpload::make("content.background")
                    ->label(__('Header Background'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required()
                    ->directory('pages/contact')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->columnSpanFull(),
                TextInput::make("content.title")
                    ->label(__('Title'))
                    ->required(),
                Textarea::make("content.description")
                    ->label(__('Description'))
                    ->required()
                    ->columnSpanFull(),
            ]),

            Section::make(__('Contact Methods'))->schema([
                Repeater::make("content.contacts")
                    ->label(__('Contacts'))
                    ->schema([
                        FileUpload::make('icon')
                            ->label(__('Icon'))
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->required()
                            ->directory('pages/contact')
                            ->getUploadedFileNameForStorageUsing($keepOriginal),
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required(),
                        Repeater::make('related_to_contact')
                            ->label(__('Contact Details'))
                            ->simple(
                                TextInput::make('text')->required()
                            )
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('contact_link')
                            ->label(__('Contact Link'))
                            ->required(),
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]),
        ];
    }

    private static function seoSchema(): array
    {
        return [
            TextInput::make('meta_title')
                ->label(__('Meta Title'))
                ->maxLength(255),
            Textarea::make('meta_description')
                ->label(__('Meta Description'))
                ->maxLength(500),
            TagsInput::make('meta_keywords')
                ->separator(',')
                ->label(__('Meta Keywords')),
            TextInput::make('canonical_url')
                ->label(__('Canonical URL'))
                ->maxLength(255),
        ];
    }

    private static function getKeepOriginalName(): \Closure
    {
        return fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();
    }
}
