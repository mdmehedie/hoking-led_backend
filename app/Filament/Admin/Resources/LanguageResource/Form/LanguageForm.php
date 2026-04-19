<?php

namespace App\Filament\Admin\Resources\LanguageResource\Form;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class LanguageForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Language Settings Tabs')->tabs([
                Tab::make(__('Language Information'))->schema([
                    TextInput::make('code')
                        ->required()
                        ->maxLength(10)
                        ->unique(ignoreRecord: true),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Select::make('direction')
                        ->options([
                            'ltr' => 'LTR',
                            'rtl' => 'RTL',
                        ])
                        ->default('ltr')
                        ->required(),
                    FileUpload::make('flag_path')
                        ->label(__('Flag'))
                        ->disk('public')
                        ->visibility('public')
                        ->directory('locales/flags')
                        ->image()
                        ->nullable(),
                ]),
                Tab::make(__('Settings'))->schema([
                    Toggle::make('is_active')
                        ->default(true)
                        ->required(),
                    Toggle::make('is_default')
                        ->helperText(__('Only one language can be default.'))
                        ->default(false),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
