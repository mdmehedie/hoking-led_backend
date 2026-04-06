<?php

namespace App\Filament\Admin\Resources\RoleResource\Form;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function form(Schema $schema): Schema
    {
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return $schema;
        }

        return $schema->schema([
            Tabs::make('Role Management Tabs')->tabs([
                Tab::make(__('Role Information'))->schema([
                    TextInput::make('name')
                        ->label(__('Role Name'))
                        ->required()
                        ->unique(ignoreRecord: true),
                    Textarea::make('description')
                        ->label(__('Description'))
                        ->maxLength(500),
                ]),
                Tab::make(__('Permissions'))->schema([
                    CheckboxList::make('permissions')
                        ->relationship('permissions', 'name')
                        ->columns(3)
                        ->gridDirection('row')
                        ->searchable()
                        ->bulkToggleable()
                        ->helperText(__('Permissions assigned to this role')),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
