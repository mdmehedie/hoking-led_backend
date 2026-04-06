<?php

namespace App\Filament\Admin\Resources\ProductResource\Form;

use App\Filament\Forms\Components\TableBuilder;
use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Http\UploadedFile;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Product Tabs')->tabs([
                Tab::make(__('General Information'))->schema(self::generalSchema()),
                Tab::make(__('Translations'))->schema(self::translationTabsSchema()),
                Tab::make(__('Media'))->schema(self::mediaSchema()),
                Tab::make(__('Technical Specifications'))->schema(self::techSpecsSchema()),
                Tab::make(__('Tags & Relations'))->schema(self::tagsSchema()),
                Tab::make(__('SEO'))->schema(self::seoSchema()),
            ])->columnSpanFull(),
        ]);
    }

    private static function generalSchema(): array
    {
        return [
            TextInput::make('slug')
                ->label(__('Slug'))
                ->unique(ignoreRecord: true)
                ->required()
                ->regex('/^[a-z0-9-]+$/')
                ->helperText(__('Only lowercase letters, numbers, and hyphens are allowed. Spaces are not permitted.'))
                ->live(debounce: 300)
                ->afterStateUpdated(function ($state, callable $set) {
                    $slug = strtolower(str_replace(' ', '-', $state));
                    $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
                    $slug = preg_replace('/-+/', '-', $slug);
                    $slug = trim($slug, '-');
                    $set('slug', $slug);
                }),
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
            Toggle::make('is_top')->label(__('Top Product')),
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
            ->schema([
                TextInput::make("title.{$locale}")
                    ->label(__('Title'))
                    ->live()
                    ->required($isDefault)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isDefault) {
                        if (!$isDefault || blank($get('slug'))) {
                            return;
                        }
                        $set('slug', static::generateUniqueSlug($state, null));
                    }),
                Textarea::make("short_description.{$locale}")
                    ->label(__('Short Description')),
                self::detailedDescriptionRepeater($locale, $isDefault),
                self::featuresRepeater($locale, $isDefault),
                self::videoEmbedsRepeater($locale, $isDefault),
            ])->columns(1);
    }

    private static function detailedDescriptionRepeater(string $locale, bool $isDefault): Repeater
    {
        return Repeater::make("detailed_description.{$locale}")
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
                    ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . "_{$locale}_" . $file->getClientOriginalName())
                    ->columnSpanFull(),
            ])
            ->columns(1)
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->createItemButtonLabel(__('Add item'))
            ->columnSpanFull();
    }

    private static function featuresRepeater(string $locale, bool $isDefault): Repeater
    {
        return Repeater::make("features.{$locale}")
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
            ->dehydrateStateUsing(fn ($state) => static::dehydrateFeaturesState($state));
    }

    private static function videoEmbedsRepeater(string $locale, bool $isDefault): Repeater
    {
        return Repeater::make("video_embeds.{$locale}")
            ->label(__('Video Embeds'))
            ->schema([
                Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'embed' => __('Embed URL'),
                        'file' => __('Self-hosted File'),
                    ])
                    ->default('embed')
                    ->live()
                    ->required($isDefault),
                TextInput::make('title')
                    ->label(__('Title'))
                    ->hidden(fn ($state, callable $get) => $get('type') === 'file'),
                TextInput::make('url')
                    ->label(__('URL'))
                    ->url()
                    ->rules(['regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/'])
                    ->hidden(fn ($state, callable $get) => $get('type') === 'file'),
                FileUpload::make('video_file')
                    ->label(__('Video File'))
                    ->disk('public')
                    ->directory('products/videos')
                    ->visibility('public')
                    ->hidden(fn ($state, callable $get) => $get('type') === 'embed')
                    ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . "_{$locale}_" . $file->getClientOriginalName()),
            ])
            ->collapsible()
            ->columnSpanFull();
    }

    private static function mediaSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();

        return [
            FileUpload::make('main_image')
                ->label(__('Main Image'))
                ->image()
                ->disk('public')
                ->directory('products/main')
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1'])
                ->getUploadedFileNameForStorageUsing($keepOriginal),
            FileUpload::make('gallery')
                ->label(__('Gallery'))
                ->multiple()
                ->image()
                ->disk('public')
                ->directory('products/gallery')
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1'])
                ->getUploadedFileNameForStorageUsing($keepOriginal),
            FileUpload::make('downloads')
                ->label(__('Downloads'))
                ->multiple()
                ->disk('public')
                ->directory('products/downloads')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal),
        ];
    }

    private static function techSpecsSchema(): array
    {
        return [
            TableBuilder::make('technical_specs')
                ->label(__('Technical Specifications Table'))
                ->initialRows(3)
                ->initialColumns(2),
        ];
    }

    private static function tagsSchema(): array
    {
        return [
            TagsInput::make('tags')
                ->label(__('Tags')),
            Select::make('related_products')
                ->label(__('Related Products'))
                ->multiple()
                ->relationship('relatedProducts', 'title')
                ->searchable()
                ->preload(),
        ];
    }

    private static function seoSchema(): array
    {
        return [
            TextInput::make('meta_title')
                ->label(__('Meta Title')),
            Textarea::make('meta_description')
                ->label(__('Meta Description')),
            Textarea::make('meta_keywords')
                ->label(__('Meta Keywords')),
            TextInput::make('canonical_url')
                ->label(__('Canonical URL')),
        ];
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
        if (blank($state) || !is_array($state) || count($state) === 0) {
            return [['feature' => '']];
        }

        foreach ($state as $key => $item) {
            if (is_array($item) && isset($item['feature']['feature'])) {
                $state[$key] = ['feature' => ''];
            }
        }

        $firstItem = reset($state);
        if (!is_array($firstItem) || !isset($firstItem['feature'])) {
            return collect($state)->map(fn ($f) => ['feature' => $f])->all();
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

        return collect($state)
            ->filter(fn ($item) => !empty($item['feature']))
            ->pluck('feature')
            ->values()
            ->all();
    }
}
