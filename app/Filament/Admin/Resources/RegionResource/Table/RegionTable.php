<?php

namespace App\Filament\Admin\Resources\RegionResource\Table;

use App\Filament\Admin\Resources\RegionResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class RegionTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Currency')
                    ->searchable(),
                TextColumn::make('language')
                    ->label('Language')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                ToggleColumn::make('is_default')
                    ->label('Default'),
                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                SelectFilter::make('is_default')
                    ->label('Default')
                    ->options([
                        '1' => 'Default',
                        '0' => 'Not Default',
                    ]),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn ($record) => RegionResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
                Action::make('delete')
                    ->label('Delete')
                    ->action(fn ($record) => $record->delete())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                BulkAction::make('toggle_active')
                    ->label('Toggle Active Status')
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                        });
                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body($count . ' regions updated successfully.')
                            ->send();
                    })
                    ->icon('heroicon-o-power'),
                BulkAction::make('change_sort_order')
                    ->label('Change Sort Order')
                    ->form([
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $count = $records->count();
                        $records->each->update(['sort_order' => $data['sort_order']]);
                        Notification::make()
                            ->success()
                            ->title('Sort Order Updated')
                            ->body($count . ' regions updated.')
                            ->send();
                    })
                    ->icon('heroicon-o-arrow-path'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }
}
