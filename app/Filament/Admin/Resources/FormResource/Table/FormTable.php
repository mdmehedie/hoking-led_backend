<?php

namespace App\Filament\Admin\Resources\FormResource\Table;

use App\Filament\Admin\Resources\FormResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
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
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn ($record) => FormResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->label(__('Delete')),
            ])
            ->bulkActions([
                BulkAction::make('delete_selected')
                    ->label('Delete Selected')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()
                            ->success()
                            ->title('Deleted')
                            ->body($count . ' items deleted successfully.')
                            ->send();
                    }),
            ]);
    }
}
