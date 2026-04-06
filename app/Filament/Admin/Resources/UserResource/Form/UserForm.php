<?php

namespace App\Filament\Admin\Resources\UserResource\Form;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('User Tabs')->tabs([
                Tab::make(__('User Information'))->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->hiddenOn('edit'),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->hiddenOn('edit')
                        ->same('password'),
                ]),
                Tab::make(__('Roles & Permissions'))->schema([
                    Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->required(),
                    Select::make('permissions')
                        ->multiple()
                        ->relationship('permissions', 'name')
                        ->preload()
                        ->label('Additional Permissions'),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
