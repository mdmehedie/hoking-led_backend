<?php

namespace App\Filament\Admin\Resources\ProjectResource\Table;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class ProjectTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label(__('Cover'))
                    ->square()
                    ->size(40),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_popular')
                    ->label(__('Popular'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_successful')
                    ->label(__('Successful'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                        'archived' => __('Archived'),
                    ]),
                TernaryFilter::make('is_featured')
                    ->label(__('Featured')),
                TernaryFilter::make('is_popular')
                    ->label(__('Popular')),
                TernaryFilter::make('is_successful')
                    ->label(__('Successful')),
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn ($record) => \App\Filament\Admin\Resources\ProjectResource::getUrl('edit', ['record' => $record]))
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
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
