<?php

namespace App\Filament\Admin\Resources\BlogResource\Form;

use App\Filament\Admin\Resources\BlogResource;
use App\Models\Locale;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogForm
{
    public static function form(Schema $schema): Schema
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return $schema
            ->schema([
                Tabs::make('Blog Tabs')->tabs([
                    Tab::make(__('General Information'))->schema([
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->rules(['regex:/^[a-z0-9-]+$/', 'no_spaces'])
                            ->helperText(__('Only lowercase letters, numbers, and hyphens are allowed. Spaces are not permitted.'))
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Convert spaces to hyphens and ensure only valid characters
                                $slug = strtolower(str_replace(' ', '-', $state));
                                $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
                                $slug = preg_replace('/-+/', '-', $slug); // Replace multiple hyphens with single
                                $slug = trim($slug, '-'); // Remove leading/trailing hyphens
                                $set('slug', $slug);
                            })
                            ->live(debounce: 300),
                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => __('Draft'),
                                'review' => __('Review'),
                                'published' => __('Published'),
                            ])
                            ->required(),
                        Hidden::make('published_at')
                            ->default(now()),
                        Hidden::make('author_id')
                            ->default(fn ($record) => $record?->author_id ?? auth()->id())
                            ->required(),
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
                                                    $set('slug', BlogResource::generateUniqueSlug($state, null));
                                                }
                                            })
                                            ->live()
                                            ->required($isDefault),
                                        Textarea::make("excerpt.{$locale}")
                                            ->label(__('Excerpt')),
                                        \App\Filament\Forms\Components\CustomRichEditor::make("content.{$locale}")
                                            ->label(__('Content'))
                                            ->required($isDefault),
                                        FileUpload::make("image_path.{$locale}")
                                            ->label(__('Image'))
                                            ->image()
                                            ->directory('blogs')
                                            ->imageEditor()
                                            ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                                    ]);
                            })->all()
                        ),
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
}
