<?php

namespace App\Filament\Admin\Resources\AnalyticsEventResource\Form;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class AnalyticsEventForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Analytics Event Tabs')->tabs([
                    Tab::make(__('Event Information'))->schema([
                        TextInput::make('event_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('page')
                            ->maxLength(255),
                        TextInput::make('url')
                            ->maxLength(500),
                        DateTimePicker::make('event_time')
                            ->required(),
                    ]),
                    Tab::make(__('Technical Details'))->schema([
                        Textarea::make('user_agent')
                            ->rows(3),
                        KeyValue::make('parameters')
                            ->label('Event Parameters')
                            ->keyLabel('Parameter')
                            ->valueLabel('Value')
                            ->addable()
                            ->deletable(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->nullable(),
                    ]),
                ])->columnSpanFull(),
            ]);
    }
}
