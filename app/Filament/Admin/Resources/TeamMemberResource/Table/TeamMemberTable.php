<?php

namespace App\Filament\Admin\Resources\TeamMemberResource\Table;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TeamMemberTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->circular()
                    ->size(50),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label(__('Position'))
                    ->searchable()
                    ->toggleable(),
                BooleanColumn::make('status')
                    ->label(__('Status')),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Inactive'),
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->label(__('Edit')),
                \Filament\Actions\DeleteAction::make()->label(__('Delete')),
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
                BulkAction::make('change_status')
                    ->label(__('Change Status'))
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                '0' => __('Inactive'),
                                '1' => __('Active'),
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each->update(['status' => $data['status']]);
                        Notification::make()
                            ->success()
                            ->title(__('Status Updated'))
                            ->body(__('Selected items updated.'))
                            ->send();
                    }),
            ]);
    }
}
