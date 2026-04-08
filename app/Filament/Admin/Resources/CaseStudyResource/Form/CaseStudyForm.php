<?php

namespace App\Filament\Admin\Resources\CaseStudyResource\Form;

use App\Models\Locale;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use App\Filament\Forms\Components\TinyEditor;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CaseStudyForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Case Study Tabs')->tabs([
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
            ->schema([
                TextInput::make("title.{$locale}")
                    ->label(__('Title'))
                    ->required($isDefault)
                    ->maxLength(255)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isDefault) {
                        if ($isDefault && blank($get('slug'))) {
                            $set('slug', static::generateUniqueSlug($state, null));
                        }
                    })
                    ->live()
                    ->required($isDefault),
                Textarea::make("excerpt.{$locale}")
                    ->label(__('Excerpt'))
                    ->maxLength(500)
                    ->columnSpanFull(),
                Section::make(__('Project Details'))->schema([
                    TextInput::make("project_details.{$locale}.product")
                        ->label(__('Product'))
                        ->maxLength(255),
                    TextInput::make("project_details.{$locale}.pixel_pitch")
                        ->label(__('Pixel Pitch'))
                        ->maxLength(255),
                    TextInput::make("project_details.{$locale}.client")
                        ->label(__('Client'))
                        ->maxLength(255),
                    TextInput::make("project_details.{$locale}.country")
                        ->label(__('Country'))
                        ->maxLength(255),
                    DatePicker::make("project_details.{$locale}.date")
                        ->label(__('Date'))
                        ->displayFormat('Y-m-d'),
                ])->columns(2),
                self::projectDescriptionRepeater($locale, $isDefault),
                self::bulletFieldsRepeater($locale, $isDefault),
            ])->columns(1);
    }

    private static function projectDescriptionRepeater(string $locale, bool $isDefault): Repeater
    {
        return Repeater::make("project_description.{$locale}")
            ->label(__('Project Description'))
            ->schema([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required($isDefault)
                    ->maxLength(255)
                    ->columnSpanFull(),
                TinyEditor::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label(__('Image'))
                    ->image()
                    ->disk('public')
                    ->directory('case-studies/descriptions')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . "_{$locale}_" . $file->getClientOriginalName())
                    ->columnSpanFull(),
            ])
            ->columns(1)
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->createItemButtonLabel(__('Add item'))
            ->columnSpanFull();
    }

    private static function bulletFieldsRepeater(string $locale, bool $isDefault): Repeater
    {
        return Repeater::make("bullet_fields.{$locale}")
            ->label(__('Bullet Points'))
            ->schema([
                TextInput::make('value')
                    ->label('')
                    ->placeholder(__('Enter a bullet point'))
                    ->required($isDefault)
                    ->maxLength(255),
            ])
            ->columns(1)
            ->itemLabel(fn (array $state): ?string => $state['value'] ?? null)
            ->createItemButtonLabel(__('Add bullet point'))
            ->columnSpanFull()
            ->default([])
            ->formatStateUsing(fn ($state) => static::formatBulletState($state))
            ->dehydrateStateUsing(fn ($state) => static::dehydrateBulletsState($state));
    }

    private static function mediaSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();

        return [
            FileUpload::make('image_path')
                ->label(__('Featured Image'))
                ->image()
                ->disk('public')
                ->directory('case-studies/images')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal)
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
            FileUpload::make('slider_images')
                ->label(__('Slider Images'))
                ->multiple()
                ->image()
                ->disk('public')
                ->directory('case-studies/slider')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal)
                ->imageEditor()
                ->reorderable(),
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

    private static function generateUniqueSlug(?string $title, ?int $ignoreId = null): string
    {
        if (blank($title)) {
            return '';
        }

        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (static::slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = \App\Models\CaseStudy::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }

    private static function formatBulletState($state): array
    {
        if (blank($state) || !is_array($state) || count($state) === 0) {
            return [['value' => '']];
        }

        $firstItem = reset($state);
        if (!is_array($firstItem) || !isset($firstItem['value'])) {
            return collect($state)->map(fn ($item) => ['value' => $item])->all();
        }

        return $state;
    }

    private static function dehydrateBulletsState($state): array
    {
        if (!is_array($state)) {
            return $state;
        }

        return collect($state)
            ->pluck('value')
            ->filter()
            ->values()
            ->all();
    }
}
