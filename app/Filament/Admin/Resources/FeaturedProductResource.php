<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeaturedProductResource\Table\FeaturedProductTable;
use App\Filament\Admin\Resources\FeaturedProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FeaturedProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Star;

    protected static ?string $navigationLabel = 'Featured Products';

    public static function getNavigationLabel(): string
    {
        return __('Featured Products');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Product Management');
    }

    protected static ?string $modelLabel = 'Featured Product';

    protected static ?string $pluralModelLabel = 'Featured Products';

    public static function table(Table $table): Table
    {
        return FeaturedProductTable::table($table);
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
            'index' => Pages\ListFeaturedProducts::route('/'),
        ];
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
}
