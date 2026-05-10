<?php

namespace App\Filament\Admin\Resources\BlogResource\Form;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use App\Filament\Forms\Components\TinyEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class BlogForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Blog Tabs')->tabs([
                Tab::make(__('Blog Content'))->schema(self::contentSchema()),
                Tab::make(__('SEO'))->schema(self::seoSchema()),
            ])->columnSpanFull(),
        ]);
    }

    private static function contentSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();

        return [
            TextInput::make('title')
                ->label(__('Title'))
                ->required()
                ->maxLength(255)
                ->live(debounce: 300)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if (blank($get('slug'))) {
                        $set('slug', static::generateUniqueSlug($state, null));
                    }
                }),
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
            Toggle::make('is_popular')
                ->label(__('Is Popular'))
                ->default(false),
            FileUpload::make('image_path')
                ->label(__('Featured Image'))
                ->image()
                ->disk('public')
                ->directory('blogs/images')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal)
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
            Textarea::make('excerpt')
                ->label(__('Excerpt'))
                ->maxLength(500)
                ->columnSpanFull(),
            TinyEditor::make('content')
                ->label(__('Content'))
                ->required()
                ->columnSpanFull(),
            Hidden::make('author_id')
                ->default(fn () => auth()->id()),
            Hidden::make('published_at')
                ->default(fn () => now()),
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
        $query = \App\Models\Blog::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }
}
