<?php

namespace App\Filament\Admin\Resources\FormResource\Table;

use Filament\Tables\Actions\Action;

use App\Filament\Admin\Resources\FormResource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class FormTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Form Name'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
                BooleanColumn::make('email_notifications')
                    ->label(__('Email Notifications')),
                BooleanColumn::make('store_leads')
                    ->label(__('Store Leads')),
            ])
            ->recordUrl(fn ($record) => FormResource::getUrl('edit', ['record' => $record]))
            ->filters([
                SelectFilter::make('email_notifications')
                    ->label(__('Email Notifications'))
                    ->options([
                        '0' => __('No'),
                        '1' => __('Yes'),
                    ]),
                SelectFilter::make('store_leads')
                    ->label(__('Store Leads'))
                    ->options([
                        '0' => __('No'),
                        '1' => __('Yes'),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->label(__('Delete')),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
