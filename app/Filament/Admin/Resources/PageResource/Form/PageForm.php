<?php

namespace App\Filament\Admin\Resources\PageResource\Form;

use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use App\Filament\Forms\Components\TinyEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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
                TinyEditor::make("content.{$locale}")
                    ->label(__('Content'))
                    ->required($isDefault)
                    ->columnSpanFull(),
            ])->columns(1);
    }

    private static function mediaSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();

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
        $query = \App\Models\Page::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }
}
