<?php

namespace App\Filament\Admin\Resources\CategoryResource\Form;

use Filament\Forms\Components\FileUpload;
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
                Tab::make(__('General Information'))->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->afterStateUpdated(function ($state, callable $set, $context) {
                            $record = $context['record'] ?? null;
                            if ($record === null) {
                                $set('slug', static::generateUniqueSlug($state, $record?->id));
                            }
                        })
                        ->live()
                        ->required(),
                    TextInput::make('slug')->label(__('Slug'))->unique(ignoreRecord: true)->required()
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
                    Textarea::make('description')->label(__('Description')),
                    FileUpload::make('thumbnail')
                        ->label(__('Thumbnail'))
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('categories/thumbnails')
                        ->visibility('public')
                        ->nullable(),
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
                    Toggle::make('is_visible')->label(__('Visible'))->default(true),
                ]),
                Tab::make(__('SEO'))->schema([
                    TextInput::make('meta_title')->label(__('Meta Title')),
                    Textarea::make('meta_description')->label(__('Meta Description')),
                    Textarea::make('meta_keywords')->label(__('Meta Keywords')),
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
