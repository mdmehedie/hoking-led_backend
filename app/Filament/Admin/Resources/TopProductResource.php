<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TopProductResource\Table\TopProductTable;
use App\Filament\Admin\Resources\TopProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TopProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Top Products');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Products';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view any product');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view product');
    }

    public static function table(Table $table): Table
    {
        return TopProductTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopProducts::route('/'),
        ];
    }
}
