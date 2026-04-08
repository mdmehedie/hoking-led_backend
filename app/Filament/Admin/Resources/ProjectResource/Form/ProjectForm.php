<?php

namespace App\Filament\Admin\Resources\ProjectResource\Form;

use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use App\Filament\Forms\Components\TinyEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Project Tabs')->tabs([
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
                ->regex('/^[a-z0-9-]+$/')
                ->helperText(__('Only lowercase letters, numbers, and hyphens are allowed.'))
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
                    'published' => __('Published'),
                    'archived' => __('Archived'),
                ])
                ->default('draft')
                ->required(),
            TextInput::make('sort_order')
                ->label(__('Sort Order'))
                ->numeric()
                ->default(0),
            Toggle::make('is_featured')->label(__('Featured')),
            Toggle::make('is_popular')->label(__('Popular')),
            Toggle::make('is_successful')->label(__('Successful')),
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
                        if (!$isDefault || blank($get('slug'))) {
                            return;
                        }
                        $set('slug', static::generateUniqueSlug($state, null));
                    })
                    ->live(),
                TextInput::make("secondary_title.{$locale}")
                    ->label(__('Secondary Title'))
                    ->maxLength(255),
                Textarea::make("excerpt.{$locale}")
                    ->label(__('Excerpt'))
                    ->maxLength(500)
                    ->columnSpanFull(),
                TinyEditor::make("description.{$locale}")
                    ->label(__('Description'))
                    ->required($isDefault)
                    ->columnSpanFull(),
            ])->columns(2);
    }

    private static function mediaSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();

        return [
            FileUpload::make('cover_image')
                ->label(__('Cover Image'))
                ->image()
                ->disk('public')
                ->directory('projects/cover')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal),
            FileUpload::make('gallery')
                ->label(__('Gallery'))
                ->multiple()
                ->image()
                ->disk('public')
                ->directory('projects/gallery')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal),
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
        $query = \App\Models\Project::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }
}
