<?php

namespace App\Filament\Admin\Resources\SliderResource\Table;

use App\Models\Slider;
use Filament\Actions\BulkAction;
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
                ImageColumn::make('background_image')->label(__('Background'))->circular()->disk('public'),
                ImageColumn::make('foreground_image')->label(__('Foreground'))->circular()->disk('public'),
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
                EditAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('update', $record)),
                DeleteAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('delete', $record)),
            ])
            ->bulkActions([
                BulkAction::make('delete_selected')
                    ->label(__('Delete Selected'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()->can('delete', new Slider()))
                    ->action(function (Collection $records) {
                        abort_unless(auth()->user()->can('delete', new Slider()), 403);

                        $authorized = $records->filter(
                            fn ($record): bool => auth()->user()->can('delete', $record)
                        );

                        $count = $records->count();
                        $authorized->each->delete();
                        Notification::make()
                            ->success()
                            ->title(__('Deleted'))
                            ->body($authorized->count() . ' ' . __('items deleted successfully.'))
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
                    ->visible(fn () => auth()->user()->can('update', new Slider()))
                    ->action(function (Collection $records, array $data) {
                        abort_unless(auth()->user()->can('update', new Slider()), 403);

                        $authorized = $records->filter(
                            fn ($record): bool => auth()->user()->can('update', $record)
                        );

                        $authorized->each->update(['status' => $data['status']]);
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
