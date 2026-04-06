<?php

namespace App\Filament\Admin\Resources\FormResource\Form;

use Filament\Forms\Components;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class FormForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Form Builder Tabs')->tabs([
                    Tab::make(__('Basic Settings'))->schema([
                        TextInput::make('name')
                            ->label(__('Form Name'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('success_message')
                            ->label(__('Success Message')),
                        Toggle::make('email_notifications')
                            ->label(__('Enable Email Notifications')),
                        TagsInput::make('notification_emails')
                            ->label(__('Notification Emails'))
                            ->placeholder(__('Enter email addresses'))
                            ->visible(fn ($get) => $get('email_notifications')),
                        Toggle::make('store_leads')
                            ->label(__('Store Leads'))
                            ->default(true),
                    ]),
                    Tab::make(__('Form Fields'))->schema([
                        Builder::make('fields')
                            ->label(__('Form Fields'))
                            ->blocks([
                                Block::make('text')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label(__('Label'))
                                            ->required(),
                                        TextInput::make('placeholder')
                                            ->label(__('Placeholder')),
                                        Toggle::make('required')
                                            ->label(__('Required')),
                                    ])
                                    ->label(__('Text Input')),
                                Block::make('email')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label(__('Label'))
                                            ->required(),
                                        TextInput::make('placeholder')
                                            ->label(__('Placeholder')),
                                        Toggle::make('required')
                                            ->label(__('Required')),
                                    ])
                                    ->label(__('Email Input')),
                                Block::make('textarea')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label(__('Label'))
                                            ->required(),
                                        TextInput::make('placeholder')
                                            ->label(__('Placeholder')),
                                        Toggle::make('required')
                                            ->label(__('Required')),
                                    ])
                                    ->label(__('Textarea')),
                                Block::make('select')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label(__('Label'))
                                            ->required(),
                                        Repeater::make('options')
                                            ->label(__('Options'))
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label(__('Label'))
                                                    ->required(),
                                                TextInput::make('value')
                                                    ->label(__('Value'))
                                                    ->required(),
                                            ])
                                            ->collapsible(),
                                        Toggle::make('required')
                                            ->label(__('Required')),
                                    ])
                                    ->label(__('Select Dropdown')),
                            ])
                            ->addActionAlignment('center')
                            ->columnSpanFull(),
                    ]),
                ])->columnSpanFull(),
            ]);
    }
}
