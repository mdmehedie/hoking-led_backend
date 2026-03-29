<?php

namespace App\Filament\Admin\Resources\ProductResource\Form;

use App\Filament\Admin\Resources\ProductResource;
use App\Models\Locale;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductForm
{
    public static function form(Schema $schema): Schema
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return $schema->schema([
            Tabs::make('Product Tabs')->tabs([
                Tab::make(__('General Information'))->schema([
                    TextInput::make('slug')
                        ->label(__('Slug'))
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->rules(['regex:/^[a-z0-9-]+$/', 'no_spaces'])
                        ->helperText(__('Only lowercase letters, numbers, and hyphens are allowed. Spaces are not permitted.'))
                        ->afterStateUpdated(function ($state, callable $set) {
                            $slug = strtolower(str_replace(' ', '-', $state));
                            $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
                            $slug = preg_replace('/-+/', '-', $slug);
                            $slug = trim($slug, '-');
                            $set('slug', $slug);
                        })
                        ->live(debounce: 300),
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label(__('Category'))
                        ->nullable(),
                    Select::make('status')
                        ->label(__('Status'))
                        ->options([
                            'draft' => __('Draft'),
                            'published' => __('Published'),
                            'archived' => __('Archived'),
                        ])
                        ->required(),
                    Hidden::make('published_at')->default(now()),
                    Toggle::make('is_featured')->label(__('Featured Product')),
                ]),
                Tab::make(__('Translations'))->schema([
                    Tabs::make('Language Tabs')->tabs(
                        collect($activeLocales)->map(function (string $locale) use ($defaultLocale) {
                            $isDefault = $locale === $defaultLocale;

                            return Tab::make(strtoupper($locale))
                                ->schema([
                                    TextInput::make("title.{$locale}")
                                        ->label(__('Title'))
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isDefault) {
                                            if (!$isDefault) {
                                                return;
                                            }

                                            if (blank($get('slug'))) {
                                                $set('slug', static::generateUniqueSlug($state, null));
                                            }
                                        })
                                        ->live()
                                        ->required($isDefault),
                                    Textarea::make("short_description.{$locale}")
                                        ->label(__('Short Description')),
                                    Repeater::make("detailed_description.{$locale}")
                                        ->label(__('Detailed Description'))
                                        ->schema([
                                            TextInput::make('title')
                                                ->label(__('Title'))
                                                ->required($isDefault)
                                                ->maxLength(255)
                                                ->columnSpanFull(),
                                            Textarea::make('description')
                                                ->label(__('Description'))
                                                ->maxLength(1000)
                                                ->columnSpanFull(),
                                            FileUpload::make('image')
                                                ->label(__('Image'))
                                                ->image()
                                                ->disk('public')
                                                ->directory('products/descriptions')
                                                ->visibility('public')
                                                ->required($isDefault)
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(1)
                                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                        ->createItemButtonLabel(__('Add item'))
                                        ->columnSpanFull(),
                                    Repeater::make("features.{$locale}")
                                        ->label(__('Key Features'))
                                        ->schema([
                                            TextInput::make('feature')
                                                ->label(__('Feature'))
                                                ->required($isDefault)
                                                ->maxLength(255)
                                                ->placeholder(__('Add a feature')),
                                        ])
                                        ->createItemButtonLabel(__('Add item'))
                                        ->columnSpanFull()
                                        ->default([])
                                        ->formatStateUsing(fn ($state) => static::formatFeaturesState($state))
                                        ->dehydrateStateUsing(fn ($state) => static::dehydrateFeaturesState($state)),
                                ])->columns(1);
                        })->all()
                    ),
                ]),
                Tab::make(__('Media'))->schema([
                    FileUpload::make('main_image')
                        ->label(__('Main Image'))
                        ->image()
                        ->disk('public')
                        ->directory('products/main')
                        ->visibility('public')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                    FileUpload::make('gallery')
                        ->label(__('Gallery'))
                        ->multiple()
                        ->image()
                        ->disk('public')
                        ->directory('products/gallery')
                        ->visibility('public')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                    Repeater::make('video_embeds')
                        ->label(__('Video Embeds'))
                        ->schema([
                            Select::make('type')
                                ->label(__('Type'))
                                ->options([
                                    'embed' => __('Embed URL'),
                                    'file' => __('Self-hosted File'),
                                ])
                                ->default('embed')
                                ->live(),
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->visible(fn ($get) => $get('type') === 'embed'),
                            TextInput::make('url')
                                ->label(__('URL'))
                                ->url()
                                ->rules(['regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/'])
                                ->visible(fn ($get) => $get('type') === 'embed'),
                            FileUpload::make('video_file')
                                ->label(__('Video File'))
                                ->visible(fn ($get) => $get('type') === 'file')
                                ->disk('public')
                                ->directory('products/videos')
                                ->visibility('public'),
                        ])
                        ->collapsible()
                        ->required(false),
                    FileUpload::make('downloads')
                        ->label(__('Downloads'))
                        ->multiple()
                        ->disk('public')
                        ->directory('products/downloads')
                        ->visibility('public'),
                ]),
                Tab::make(__('Technical Specifications'))->schema([
                    \App\Filament\Forms\Components\TableBuilder::make('technical_specs')
                        ->label(__('Technical Specifications Table'))
                        ->initialRows(3)
                        ->initialColumns(2),
                ]),
                Tab::make(__('Tags & Relations'))->schema([
                    TagsInput::make('tags')
                        ->label(__('Tags')),
                    Select::make('related_products')
                        ->label(__('Related Products'))
                        ->multiple()
                        ->relationship('relatedProducts', 'title')
                        ->searchable()
                        ->preload(),
                ]),
                Tab::make(__('SEO'))->schema([
                    TextInput::make('meta_title')
                        ->label(__('Meta Title')),
                    Textarea::make('meta_description')
                        ->label(__('Meta Description')),
                    Textarea::make('meta_keywords')
                        ->label(__('Meta Keywords')),
                    TextInput::make('canonical_url')
                        ->label(__('Canonical URL')),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'products';
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    /**
     * Format features state for display in repeater.
     */
    private static function formatFeaturesState($state): array
    {
        // Handle empty or invalid state
        if (blank($state) || !is_array($state) || count($state) === 0) {
            return [['feature' => '']];
        }

        // Fix double-nested structure (Filament wrapping issue)
        foreach ($state as $key => $item) {
            if (is_array($item) && isset($item['feature']['feature'])) {
                $state[$key] = ['feature' => ''];
            }
        }

        // Convert simple array to repeater format
        $firstItem = reset($state);
        if (!is_array($firstItem) || !isset($firstItem['feature'])) {
            return collect($state)->map(fn($f) => ['feature' => $f])->all();
        }

        return $state;
    }

    /**
     * Dehydrate features state for storage.
     */
    private static function dehydrateFeaturesState($state): array
    {
        if (!is_array($state)) {
            return $state;
        }

        // Convert repeater format to simple array, filtering empty values
        return collect($state)
            ->filter(fn($item) => !empty($item['feature']))
            ->pluck('feature')
            ->values()
            ->all();
    }
}
