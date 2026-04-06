<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsResource\Form\NewsForm;
use App\Filament\Admin\Resources\NewsResource\Pages;
use App\Filament\Admin\Resources\NewsResource\Table\NewsTable;
use App\Models\News;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationLabel = 'News';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'news-items';

    public static function getNavigationLabel(): string
    {
        return __('News');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    // Override navigation URL to use the correct slug
    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create news');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit news');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete news');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view news');
    }

    public static function form(Schema $schema): Schema
    {
        return NewsForm::configure($schema, [static::class, 'generateUniqueSlug']);
    }

    public static function table(Table $table): Table
    {
        return NewsTable::configure($table);
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'news';
        $baseSlug = \Illuminate\Support\Str::slug($title);
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
