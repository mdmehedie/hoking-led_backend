<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
        return $schema->schema([
            Tabs::make('User Tabs')->tabs([
                Tab::make(__('User Information'))->schema([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->hiddenOn('edit'),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->hiddenOn('edit')
                        ->same('password'),
                ]),
                Tab::make(__('Roles & Permissions'))->schema([
                    Forms\Components\Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('permissions')
                        ->multiple()
                        ->relationship('permissions', 'name')
                        ->preload()
                        ->label('Additional Permissions'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
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
