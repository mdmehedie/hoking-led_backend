<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RegionResource\Form\RegionForm;
use App\Filament\Admin\Resources\RegionResource\Table\RegionTable;
use App\Filament\Admin\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::GlobeAmericas;

    protected static ?string $navigationLabel = 'Regions';


    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function canViewAny(): bool
    {
        return false;
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
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return RegionForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return RegionTable::table($table);
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
