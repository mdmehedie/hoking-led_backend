<?php

namespace App\Filament\Admin\Resources\UserResource\Form;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->label(__('Password')),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->same('password')
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
