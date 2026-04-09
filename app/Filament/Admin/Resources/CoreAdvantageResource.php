<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CoreAdvantageResource\Form\CoreAdvantageForm;
use App\Filament\Admin\Resources\CoreAdvantageResource\Table\CoreAdvantageTable;
use App\Filament\Admin\Resources\CoreAdvantageResource\Pages;
use App\Models\CoreAdvantage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CoreAdvantageResource extends Resource
{
    protected static ?string $model = CoreAdvantage::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Core Advantages';

    protected static ?string $slug = 'core-advantages';


    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view coreadvantage');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create coreadvantage');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit coreadvantage');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete coreadvantage');
    }

    public static function canView($record): bool
    {
        return auth()->check();
    }

    public static function form(Schema $schema): Schema
    {
        return CoreAdvantageForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return CoreAdvantageTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoreAdvantages::route('/'),
            'create' => Pages\CreateCoreAdvantage::route('/create'),
            'edit' => Pages\EditCoreAdvantage::route('/{record}/edit'),
        ];
    }
}
