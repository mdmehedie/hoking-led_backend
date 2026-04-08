<?php

namespace App\Filament\Admin\Resources\TeamMemberResource\Form;

use App\Filament\Forms\Components\TinyEditor;
use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Http\UploadedFile;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Team Member Tabs')->tabs([
                Tab::make(__('General'))->schema(self::generalSchema()),
                Tab::make(__('Translations'))->schema(self::translationTabsSchema()),
                Tab::make(__('Media'))->schema(self::mediaSchema()),
                Tab::make(__('Social Links'))->schema(self::socialSchema()),
                Tab::make(__('SEO'))->schema(self::seoSchema()),
            ])->columnSpanFull(),
        ]);
    }

    private static function generalSchema(): array
    {
        return [
            TextInput::make('slug')
                ->label(__('Slug'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->regex('/^[a-z0-9-]+$/')
                ->helperText(__('Only lowercase letters, numbers, and hyphens.'))
                ->live(debounce: 300)
                ->afterStateUpdated(function ($state, callable $set) {
                    $slug = strtolower(str_replace(' ', '-', $state));
                    $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
                    $slug = preg_replace('/-+/', '-', $slug);
                    $slug = trim($slug, '-');
                    $set('slug', $slug);
                }),
            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('Phone'))
                ->maxLength(50),
            TextInput::make('sort_order')
                ->label(__('Sort Order'))
                ->numeric()
                ->default(0)
                ->helperText(__('Lower numbers appear first.')),
            Toggle::make('status')
                ->label(__('Active'))
                ->default(true),
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
                TextInput::make("name.{$locale}")
                    ->label(__('Name'))
                    ->required($isDefault)
                    ->maxLength(255),
                TextInput::make("position.{$locale}")
                    ->label(__('Position'))
                    ->required($isDefault)
                    ->maxLength(255),
                TinyEditor::make("bio.{$locale}")
                    ->label(__('Bio'))
                    ->columnSpanFull(),
            ])->columns(1);
    }

    private static function mediaSchema(): array
    {
        return [
            FileUpload::make('photo')
                ->label(__('Photo'))
                ->image()
                ->required()
                ->disk('public')
                ->directory('team-members')
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '3:4'])
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName()),
        ];
    }

    private static function socialSchema(): array
    {
        return [
            Repeater::make('social_links')
                ->label(__('Social Links'))
                ->schema([
                    TextInput::make('platform')
                        ->label(__('Platform'))
                        ->maxLength(50)
                        ->placeholder('e.g. LinkedIn, Twitter, Facebook'),
                    TextInput::make('url')
                        ->label(__('URL'))
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://...'),
                ])
                ->columns(2)
                ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                ->createItemButtonLabel(__('Add social link'))
                ->columnSpanFull()
                ->default([['platform' => '', 'url' => '']]),
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
                ->maxLength(500)
                ->columnSpanFull(),
            Textarea::make('meta_keywords')
                ->label(__('Meta Keywords'))
                ->columnSpanFull(),
            TextInput::make('canonical_url')
                ->label(__('Canonical URL'))
                ->maxLength(255)
                ->columnSpanFull(),
        ];
    }
}
