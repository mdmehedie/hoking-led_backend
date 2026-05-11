<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BrandResource\Form\BrandForm;
use App\Filament\Admin\Resources\BrandResource\Table\BrandTable;
use App\Filament\Admin\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Brands';

    protected static ?string $slug = 'brands';


    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('viewAny', Brand::class);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create', Brand::class);
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete', $record);
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view', $record);
    }

    public static function form(Schema $schema): Schema
    {
        return BrandForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
