<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Form\ProductForm;
use App\Filament\Admin\Resources\ProductResource\Table\ProductTable;
use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $slug = 'product-items';


    protected static ?int $navigationSort = 0;

    // Override navigation URL to use the correct slug
    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Products';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create product');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit product');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete product');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view product');
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductTable::table($table);
    }

    public static function canAccessResource(): bool
    {
        return auth()->user()->hasRole('Super Admin');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
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
