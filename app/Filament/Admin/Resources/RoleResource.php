<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Form\RoleForm;
use App\Filament\Admin\Resources\RoleResource\Table\RoleTable;
use App\Filament\Admin\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationLabel = 'Roles';


    protected static ?int $navigationSort = 1;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    public static function canAccessResource(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Admin']);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create role');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit role');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete role');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view role');
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return RoleTable::table($table);
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
