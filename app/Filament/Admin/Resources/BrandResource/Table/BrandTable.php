<?php

namespace App\Filament\Admin\Resources\BrandResource\Table;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class BrandTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->square()
                    ->size(40),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('website_url')
                    ->label(__('Website'))
                    ->url(fn ($state) => $state, true)
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Active')),
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn ($record) => \App\Filament\Admin\Resources\BrandResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
                Action::make('delete')
                    ->label(__('Delete'))
                    ->action(fn ($record) => $record->delete())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
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
