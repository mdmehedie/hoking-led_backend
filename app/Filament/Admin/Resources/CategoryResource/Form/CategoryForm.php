<?php

namespace App\Filament\Admin\Resources\CategoryResource\Form;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class CategoryForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Category Tabs')->tabs([
                Tab::make(__('Category Details'))->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->live()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (blank($get('slug'))) {
                                $set('slug', static::generateUniqueSlug($state, null));
                            }
                        }),
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
                    Select::make('parent_id')
                        ->label(__('Parent Category'))
                        ->options(function () {
                            return \App\Models\Category::query()
                                ->where('is_visible', true)
                                ->orderBy('name')
                                ->get()
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Toggle::make('is_visible')
                        ->label(__('Visible'))
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label(__('Sort Order'))
                        ->numeric()
                        ->default(0)
                        ->helperText(__('Lower numbers appear first')),
                    Textarea::make('description')
                        ->label(__('Description'))
                        ->rows(6)
                        ->columnSpanFull(),
                    FileUpload::make('thumbnail')
                        ->label(__('Thumbnail'))
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('categories/thumbnails')
                        ->visibility('public')
                        ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName())
                        ->nullable(),
                    FileUpload::make('icon')
                        ->label(__('Icon'))
                        ->image()
                        ->disk('public')
                        ->directory('categories/icons')
                        ->visibility('public')
                        ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName())
                        ->nullable(),
                ]),
                Tab::make(__('SEO'))->schema([
                    TextInput::make('meta_title')->label(__('Meta Title')),
                    Textarea::make('meta_description')->label(__('Meta Description')),
                    TagsInput::make('meta_keywords')->separator(',')->label(__('Meta Keywords')),
                    TextInput::make('canonical_url')->label(__('Canonical URL')),
                ]),
            ])->columnSpanFull()
        ]);
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'categories';
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
