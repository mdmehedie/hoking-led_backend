<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PageResource\Form\PageForm;
use App\Filament\Admin\Resources\PageResource\Table\PageTable;
use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationLabel = 'Pages';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('Pages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document';

    public static function canCreate(): bool
    {
        return auth()->user()->can('create page');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit page');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete page');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view page');
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return PageTable::table($table);
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function generateUniqueSlug($title, $id = null)
    {
        $table = 'pages';
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
