<?php

namespace App\Filament\Admin\Resources\CategoryResource\Table;

use App\Filament\Admin\Resources\CategoryResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class CategoryTable
{
    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('icon')
                ->label(__('Icon'))
                ->square()
                ->size(30),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->searchable()->sortable(),
            TextColumn::make('parent.name')->label(__('Parent'))->sortable()->placeholder('-'),
            BooleanColumn::make('is_visible')->label(__('Visible'))->sortable(),
        ])
        ->defaultSort('name')
        ->actions([
            Action::make('edit')
                ->url(fn ($record) => CategoryResource::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-o-pencil'),
            Action::make('delete')
                ->action(fn ($record) => $record->delete())
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash'),
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
