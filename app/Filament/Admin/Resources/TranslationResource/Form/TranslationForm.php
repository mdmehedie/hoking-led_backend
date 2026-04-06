<?php

namespace App\Filament\Admin\Resources\TranslationResource\Form;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TranslationForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Translation Settings Tabs')->tabs([
                Tab::make(__('Translation'))->schema([
                    TextInput::make('key')
                        ->required()
                        ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                            return $rule->where('locale', $get('locale'));
                        }),
                    Select::make('locale')
                        ->required()
                        ->options(fn () => array_combine(config('app.supported_locales', ['en']), config('app.supported_locales', ['en']))),
                    Textarea::make('value')
                        ->rows(4)
                        ->nullable(),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
