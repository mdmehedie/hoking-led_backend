<?php

namespace App\Filament\Admin\Resources\PageResource\Form;

use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;

class PageForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Page Tabs')->tabs([
                Tab::make(__('General Information'))->schema(self::generalSchema()),
                Tab::make(__('Translations'))->schema(self::translationTabsSchema()),
                Tab::make(__('Media'))->schema(self::mediaSchema()),
                Tab::make(__('SEO'))->schema(self::seoSchema()),
            ])->columnSpanFull(),
        ]);
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

    private static function translationTabsSchema(): array
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return [
            Tabs::make('Language Tabs')->tabs(
                collect($activeLocales)->map(fn (string $locale) => self::localeTabSchema($locale, $locale === $defaultLocale))->all()
            ),
        ];
    }

    private static function localeTabSchema(string $locale, bool $isDefault): Tab
    {
        return Tab::make(strtoupper($locale))
            ->schema(function ($record) use ($locale, $isDefault) {
                $slug = $record?->slug;

                if ($slug === 'company') {
                    return self::companyPageSchema($locale, $isDefault);
                }

                if ($slug === 'about-us') {
                    return self::aboutUsPageSchema($locale, $isDefault);
                }

                if ($slug === 'after-sale-service') {
                    return self::afterSaleServicePageSchema($locale, $isDefault);
                }

                if ($slug === 'contact') {
                    return self::contactPageSchema($locale, $isDefault);
                }

                // Default schema for other pages
                return [
                    TextInput::make("title.{$locale}")
                        ->label(__('Title'))
                        ->required($isDefault)
                        ->maxLength(255),
                    Textarea::make("excerpt.{$locale}")
                        ->label(__('Excerpt'))
                        ->required($isDefault)
                        ->maxLength(500),
                    \App\Filament\Forms\Components\TinyEditor::make("content.{$locale}")
                        ->label(__('Content'))
                        ->required($isDefault)
                        ->columnSpanFull(),
                ];
            })->columns(2);
    }

    private static function companyPageSchema(string $locale, bool $isDefault): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            TextInput::make("title.{$locale}")
                ->label(__('Title'))
                ->required($isDefault)
                ->maxLength(255),
            Textarea::make("excerpt.{$locale}")
                ->label(__('Excerpt'))
                ->required($isDefault)
                ->maxLength(500),

            Section::make(__('Hero Section'))->schema([
                FileUpload::make("content.{$locale}.hero_bg")
                    ->label(__('Hero Background'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required($isDefault)
                    ->directory('pages/company')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->columnSpanFull(),
                TextInput::make("content.{$locale}.hero_title")
                    ->label(__('Hero Title'))
                    ->required($isDefault)
                    ->maxLength(255),
                TextInput::make("content.{$locale}.hero_secondary_title")
                    ->label(__('Hero Secondary Title'))
                    ->required($isDefault)
                    ->maxLength(255),
                Textarea::make("content.{$locale}.hero_description")
                    ->label(__('Hero Description'))
                    ->required($isDefault)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make(__('Banner Section'))->schema([
                FileUpload::make("content.{$locale}.banner")
                    ->label(__('Banner Image'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required($isDefault)
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->directory('pages/company')
                    ->columnSpanFull(),
            ]),

            Section::make(__('Our Company'))->schema([
                Repeater::make("content.{$locale}.our_company_description")
                    ->label(__('Company Description Paragraphs'))
                    ->required($isDefault)
                    ->simple(
                        Textarea::make('paragraph')
                            ->required()
                    )
                    ->columnSpanFull(),
            ]),

            Section::make(__('Our Factory'))->schema([
                TextInput::make("content.{$locale}.our_factory.title")
                    ->label(__('Factory Title'))
                    ->required($isDefault)
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make("content.{$locale}.our_factory.description_1")
                    ->label(__('Description 1'))
                    ->required($isDefault)
                    ->maxLength(1000),
                Textarea::make("content.{$locale}.our_factory.description_2")
                    ->label(__('Description 2'))
                    ->required($isDefault)
                    ->maxLength(1000),
                TextInput::make("content.{$locale}.our_factory.redirect_link")
                    ->label(__('Redirect Link'))
                    ->required($isDefault)
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
                FileUpload::make("content.{$locale}.our_factory.image_1")
                    ->label(__('Image 1'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->required($isDefault)
                    ->directory('pages/company'),
                FileUpload::make("content.{$locale}.our_factory.image_2")
                    ->label(__('Image 2'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->required($isDefault)
                    ->directory('pages/company'),
                FileUpload::make("content.{$locale}.our_factory.image_3")
                    ->label(__('Image 3'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->required($isDefault)
                    ->directory('pages/company'),
            ])->columns(2),

            Section::make(__('Certifications Section'))->schema([
                TextInput::make("content.{$locale}.certification_title")
                    ->label(__('Certification Title'))
                    ->required($isDefault)
                    ->maxLength(255),
                Textarea::make("content.{$locale}.certification_description")
                    ->label(__('Certification Description'))
                    ->required($isDefault)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Pillars & Growth'))->schema([
                Textarea::make("content.{$locale}.mission")
                    ->label(__('Mission'))
                    ->required($isDefault),
                Textarea::make("content.{$locale}.value")
                    ->label(__('Value'))
                    ->required($isDefault),
                Textarea::make("content.{$locale}.growth")
                    ->label(__('Growth'))
                    ->required($isDefault),
                FileUpload::make("content.{$locale}.bottom_image")
                    ->label(__('Bottom Image'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required($isDefault)
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->directory('pages/company')
                    ->columnSpanFull(),
            ])->columns(1),
        ];
    }

    private static function aboutUsPageSchema(string $locale, bool $isDefault): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            TextInput::make("title.{$locale}")
                ->label(__('Title'))
                ->required($isDefault)
                ->maxLength(255),
            Textarea::make("excerpt.{$locale}")
                ->label(__('Excerpt'))
                ->required($isDefault)
                ->maxLength(500),

            Section::make(__('Hero & Intro'))->schema([
                TextInput::make("content.{$locale}.title")
                    ->label(__('Intro Title'))
                    ->required($isDefault),
                TextInput::make("content.{$locale}.secondary_title")
                    ->label(__('Secondary Title'))
                    ->required($isDefault),
                Textarea::make("content.{$locale}.first_description")
                    ->label(__('First Description'))
                    ->required($isDefault)
                    ->columnSpanFull(),
                TextInput::make("content.{$locale}.first_description_redirect_link")
                    ->label(__('Redirect Link'))
                    ->url()
                    ->required($isDefault),
                FileUpload::make("content.{$locale}.image_1")
                    ->label(__('Image 1'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required($isDefault)
                    ->directory('pages/about-us')
                    ->getUploadedFileNameForStorageUsing($keepOriginal),
                FileUpload::make("content.{$locale}.image_2")
                    ->label(__('Image 2'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required($isDefault)
                    ->directory('pages/about-us')
                    ->getUploadedFileNameForStorageUsing($keepOriginal),
            ])->columns(2),

            Section::make(__('Stats & Service'))->schema([
                TextInput::make("content.{$locale}.service")
                    ->label(__('Service Text'))
                    ->required($isDefault),
                TextInput::make("content.{$locale}.countries_active_clients")
                    ->label(__('Countries with Active Clients'))
                    ->required($isDefault),
                TextInput::make("content.{$locale}.years_warranty")
                    ->label(__('Years Warranty'))
                    ->required($isDefault),
                TextInput::make("content.{$locale}.service_warranty")
                    ->label(__('Service Warranty'))
                    ->required($isDefault),
            ])->columns(2),

            Section::make(__('Our Process'))->schema([
                TextInput::make("content.{$locale}.our_process.title")
                    ->label(__('Process Title'))
                    ->required($isDefault)
                    ->columnSpanFull(),
                Repeater::make("content.{$locale}.our_process.steps")
                    ->label(__('Process Steps'))
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->required(),
                    ])
                    ->required($isDefault)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Mission & Vision'))->schema([
                FileUpload::make("content.{$locale}.mission_vision_image")
                    ->label(__('Main Section Image'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required($isDefault)
                    ->directory('pages/about-us')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->columnSpanFull(),
                TextInput::make("content.{$locale}.mission_vision_title")
                    ->label(__('Section Title'))
                    ->required($isDefault)
                    ->columnSpanFull(),
                
                Section::make(__('Mission'))->schema([
                    TextInput::make("content.{$locale}.mission.title")
                        ->label(__('Mission Title'))
                        ->required($isDefault),
                    FileUpload::make("content.{$locale}.mission.icon")
                        ->label(__('Mission Icon'))
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->required($isDefault)
                        ->directory('pages/about-us')
                        ->getUploadedFileNameForStorageUsing($keepOriginal),
                    Textarea::make("content.{$locale}.mission.description")
                        ->label(__('Mission Description'))
                        ->required($isDefault)
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make(__('Vision'))->schema([
                    TextInput::make("content.{$locale}.vision.title")
                        ->label(__('Vision Title'))
                        ->required($isDefault),
                    FileUpload::make("content.{$locale}.vision.icon")
                        ->label(__('Vision Icon'))
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->required($isDefault)
                        ->directory('pages/about-us')
                        ->getUploadedFileNameForStorageUsing($keepOriginal),
                    Textarea::make("content.{$locale}.vision.description")
                        ->label(__('Vision Description'))
                        ->required($isDefault)
                        ->columnSpanFull(),
                ])->columns(2),
            ]),
        ];
    }

    private static function afterSaleServicePageSchema(string $locale, bool $isDefault): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            TextInput::make("title.{$locale}")
                ->label(__('Title'))
                ->required($isDefault)
                ->maxLength(255),
            Textarea::make("excerpt.{$locale}")
                ->label(__('Excerpt'))
                ->required($isDefault)
                ->maxLength(500),

            Section::make(__('Hero Section'))->schema([
                FileUpload::make("content.{$locale}.hero_bg")
                    ->label(__('Hero Background'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->required($isDefault)
                    ->directory('pages/after-sale-service')
                    ->getUploadedFileNameForStorageUsing($keepOriginal)
                    ->columnSpanFull(),
                TextInput::make("content.{$locale}.hero_title")
                    ->label(__('Hero Title'))
                    ->required($isDefault)
                    ->maxLength(255),
                Textarea::make("content.{$locale}.hero_description")
                    ->label(__('Hero Description'))
                    ->required($isDefault)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Services'))->schema([
                Repeater::make("content.{$locale}.services")
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
                    ->required($isDefault)
                    ->columns(1)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Warranty Policy'))->schema([
                Repeater::make("content.{$locale}.quality_warranty_policy")
                    ->label(__('Our Quality Warranty Policy'))
                    ->required($isDefault)
                    ->simple(
                        Textarea::make('policy_text')
                            ->required()
                    )
                    ->columnSpanFull(),
            ]),
        ];
    }

    private static function contactPageSchema(string $locale, bool $isDefault): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            TextInput::make("title.{$locale}")
                ->label(__('Title'))
                ->required($isDefault)
                ->maxLength(255),
            Textarea::make("excerpt.{$locale}")
                ->label(__('Excerpt'))
                ->required($isDefault)
                ->maxLength(500),

            Section::make(__('Header'))->schema([
                TextInput::make("content.{$locale}.title")
                    ->label(__('Title'))
                    ->required($isDefault),
                Textarea::make("content.{$locale}.description")
                    ->label(__('Description'))
                    ->required($isDefault)
                    ->columnSpanFull(),
            ]),

            Section::make(__('Contact Methods'))->schema([
                Repeater::make("content.{$locale}.contacts")
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
                    ->required($isDefault)
                    ->columnSpanFull(),
            ]),
        ];
    }

    private static function mediaSchema(): array
    {
        $keepOriginal = self::getKeepOriginalName();

        return [
            FileUpload::make('image_path')
                ->label(__('Featured Image'))
                ->image()
                ->disk('public')
                ->directory('pages/images')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal)
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
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
            Textarea::make('meta_keywords')
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
