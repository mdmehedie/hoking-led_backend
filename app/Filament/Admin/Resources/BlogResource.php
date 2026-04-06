<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogResource\Form\BlogForm;
use App\Filament\Admin\Resources\BlogResource\Table\BlogTable;
use App\Filament\Admin\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationLabel = 'Blogs';

    public static function getNavigationLabel(): string
    {
        return __('Blogs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    public static function canCreate(): bool
    {
        return auth()->user()->can('create blog');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit blog');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete blog');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view blog');
    }

    public static function form(Schema $schema): Schema
    {
        return BlogForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }

    public static function generateUniqueSlug($title, $id = null)
    {
        $table = 'blogs';
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
     * Generate share URL for content preview
     */
    public static function generateShareUrl($record, string $contentType): string
    {
        // Get frontend URL from app settings or fallback to app URL
        $frontendUrl = \App\Models\AppSetting::first()?->frontend_url ?? config('app.url');

        // Ensure frontend URL doesn't end with /
        $frontendUrl = rtrim($frontendUrl, '/');

        // Get content type prefix from app settings with fallback
        $prefix = \App\Models\AppSetting::first()?->{$contentType . '_prefix'} ?? match($contentType) {
            'blog' => '/blog/',
            'news' => '/news/',
            'page' => '/pages/',
            'case_study' => '/case-studies/',
            'product' => '/products/',
            default => '/',
        };

        // Ensure prefix starts and ends with /
        $prefix = '/' . trim($prefix, '/') . '/';

        // Get slug
        $slug = $record->slug ?? '';

        // Construct full URL
        return $frontendUrl . $prefix . $slug;
    }
}
