<?php

namespace App\Filament\Admin\Resources\UserResource\Form;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\Rules\Password;

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
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->label(__('Password'))
                        ->rule(Password::default())
                        ->confirmed(),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->required(fn (Get $get): bool => filled($get('password')))
                        ->dehydrated(false)
                        ->label(__('Confirm Password')),
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
