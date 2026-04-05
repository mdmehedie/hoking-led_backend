<?php

namespace App\Filament\Admin\Resources\SliderResource\Table;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class SliderTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('Title'))->sortable()->searchable(),
                TextColumn::make('label')->label(__('Label'))->sortable()->searchable(),
                ImageColumn::make('background_image')->label(__('Background'))->circular(),
                ImageColumn::make('foreground_image')->label(__('Foreground'))->circular(),
                BooleanColumn::make('status')->label(__('Status')),
                TextColumn::make('sort_order')->label(__('Sort Order'))->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Inactive'),
                    ]),
            ])
            ->searchable()
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('delete_selected')
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
                        Select::make('status')
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
                            ->body(__('Selected items have been updated to') . ' ' . ($data['status'] ? __('Active') : __('Inactive')) . '.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-cog'),
            ]);
    }
}
