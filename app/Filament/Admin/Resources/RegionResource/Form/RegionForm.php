<?php

namespace App\Filament\Admin\Resources\RegionResource\Form;

use Filament\Forms\Components;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class RegionForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Region Settings Tabs')->tabs([
                    Tab::make(__('Region Information'))->schema([
                        TextInput::make('code')
                            ->label('Region Code')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->helperText('e.g., us, uk, eu'),
                        TextInput::make('name')
                            ->label('Region Name')
                            ->required()
                            ->maxLength(100)
                            ->helperText('e.g., United States, United Kingdom'),
                        TextInput::make('currency')
                            ->label('Currency')
                            ->maxLength(3)
                            ->helperText('e.g., USD, GBP, EUR'),
                        TextInput::make('timezone')
                            ->label('Timezone')
                            ->helperText('e.g., America/New_York'),
                        TextInput::make('language')
                            ->label('Language')
                            ->maxLength(10)
                            ->helperText('e.g., en, en-US'),
                    ])->columns(2),
                    Tab::make(__('Settings'))->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable this region for users'),
                        Toggle::make('is_default')
                            ->label('Default Region')
                            ->default(false)
                            ->helperText('Set as default region for new users'),
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order in which regions appear'),
                    ])->columns(3),
                ])->columnSpanFull(),
            ]);
    }
}
