<?php

namespace App\Filament\Admin\Resources\TeamMemberResource\Form;

use App\Filament\Forms\Components\TinyEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Http\UploadedFile;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Team Member Tabs')->tabs([
                Tabs\Tab::make(__('Member Information'))->schema(self::memberSchema()),
                Tabs\Tab::make(__('SEO'))->schema(self::seoSchema()),
            ])->columnSpanFull(),
        ]);
    }

    private static function memberSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();

        return [
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255)
                ->live(debounce: 300)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if (blank($get('slug'))) {
                        $set('slug', static::slugify($state));
                    }
                }),
            TextInput::make('slug')
                ->label(__('Slug'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->regex('/^[a-z0-9-]+$/')
                ->helperText(__('Only lowercase letters, numbers, and hyphens.'))
                ->live(debounce: 300)
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('slug', static::slugify($state));
                }),
            TextInput::make('position')
                ->label(__('Position'))
                ->required()
                ->maxLength(255),
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
                ->getUploadedFileNameForStorageUsing($keepOriginal),
            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('Phone'))
                ->maxLength(50),
            TinyEditor::make('bio')
                ->label(__('Bio'))
                ->columnSpanFull(),
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

    private static function slugify(?string $value): string
    {
        if (blank($value)) {
            return '';
        }

        $slug = strtolower(str_replace(' ', '-', $value));
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
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
            TagsInput::make('meta_keywords')
                ->separator(',')
                ->label(__('Meta Keywords')),
            TextInput::make('canonical_url')
                ->label(__('Canonical URL'))
                ->maxLength(255)
                ->columnSpanFull(),
        ];
    }
}
