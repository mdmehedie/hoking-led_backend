<?php

namespace App\Filament\Admin\Resources\NewsletterSubscriptionResource\Table;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class NewsletterSubscriptionTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('Email address copied'))
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label(__('First Name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_name')
                    ->label(__('Last Name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'gray' => 'unsubscribed',
                        'danger' => 'bounced',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('source')
                    ->label(__('Source'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subscribed_at')
                    ->label(__('Subscribed'))
                    ->dateTime('M j, Y')
                    ->sortable(),
                TextColumn::make('unsubscribed_at')
                    ->label(__('Unsubscribed'))
                    ->dateTime('M j, Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'active' => __('Active'),
                        'unsubscribed' => __('Unsubscribed'),
                        'bounced' => __('Bounced'),
                    ]),
                SelectFilter::make('source')
                    ->label(__('Source'))
                    ->options([
                        'website' => __('Website'),
                        'footer' => __('Footer'),
                        'popup' => __('Popup'),
                        'checkout' => __('Checkout'),
                        'landing_page' => __('Landing Page'),
                        'import' => __('Import'),
                    ]),
                TernaryFilter::make('consent_given')
                    ->label(__('Consent Given')),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('View'))
                    ->url(fn ($record) => \App\Filament\Admin\Resources\NewsletterSubscriptionResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                Action::make('activate')
                    ->label(__('Activate'))
                    ->action(fn ($record) => $record->markAsActive())
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Action::make('unsubscribe')
                    ->label(__('Unsubscribe'))
                    ->action(fn ($record) => $record->unsubscribe())
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status !== 'unsubscribed')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
                Action::make('delete')
                    ->label(__('Delete'))
                    ->action(fn ($record) => $record->delete())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                BulkAction::make('activate')
                    ->label(__('Activate Selected'))
                    ->action(function (Collection $records) {
                        $count = 0;
                        $records->each(function ($record) use (&$count) {
                            if ($record->status !== 'active') {
                                $record->markAsActive();
                                $count++;
                            }
                        });
                        Notification::make()
                            ->success()
                            ->title(__('Activated'))
                            ->body($count . ' ' . __('subscriptions activated.'))
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
                BulkAction::make('unsubscribe')
                    ->label(__('Unsubscribe Selected'))
                    ->action(function (Collection $records) {
                        $count = 0;
                        $records->each(function ($record) use (&$count) {
                            if ($record->status !== 'unsubscribed') {
                                $record->unsubscribe();
                                $count++;
                            }
                        });
                        Notification::make()
                            ->success()
                            ->title(__('Unsubscribed'))
                            ->body($count . ' ' . __('subscriptions unsubscribed.'))
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
                BulkAction::make('delete')
                    ->label(__('Delete Selected'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()
                            ->success()
                            ->title(__('Deleted'))
                            ->body($count . ' ' . __('items deleted successfully.'))
                            ->send();
                    }),
            ]);
    }
}
