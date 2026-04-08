<?php

namespace App\Filament\Admin\Resources\NewsletterSubscriptionResource\Form;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NewsletterSubscriptionForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('first_name')
                ->label(__('First Name'))
                ->nullable()
                ->maxLength(255),
            TextInput::make('last_name')
                ->label(__('Last Name'))
                ->nullable()
                ->maxLength(255),
            Select::make('status')
                ->label(__('Status'))
                ->options([
                    'pending' => __('Pending'),
                    'active' => __('Active'),
                    'unsubscribed' => __('Unsubscribed'),
                    'bounced' => __('Bounced'),
                ])
                ->required(),
            TextInput::make('source')
                ->label(__('Source'))
                ->nullable()
                ->maxLength(255),
            Textarea::make('preferences')
                ->label(__('Preferences (JSON)'))
                ->nullable()
                ->columnSpanFull(),
            DateTimePicker::make('subscribed_at')
                ->label(__('Subscribed At'))
                ->nullable(),
            DateTimePicker::make('unsubscribed_at')
                ->label(__('Unsubscribed At'))
                ->nullable(),
        ])->columns(2);
    }
}
