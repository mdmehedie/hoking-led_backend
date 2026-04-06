<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Form\UserForm;
use App\Filament\Admin\Resources\UserResource\Table\UserTable;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Users';

    public static function getNavigationLabel(): string
    {
        return __('Users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    public static function canAccessResource(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Admin']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create user');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit user');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete user');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view user');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserTable::configure($table);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
