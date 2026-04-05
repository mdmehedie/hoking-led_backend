<?php

namespace App\Filament\Admin\Resources\LeadResource\Table;

use App\Filament\Admin\Resources\LeadResource;
use App\Models\Form;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID')),
                TextColumn::make('form.name')
                    ->label(__('Form')),
                TextColumn::make('data')
                    ->label(__('Data'))
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->searchable()
            ->filters([
                SelectFilter::make('form_id')
                    ->label(__('Form'))
                    ->options(Form::pluck('name', 'id')),
                Filter::make('created_at')
                    ->label(__('Created Date'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('From')),
                        DatePicker::make('created_until')
                            ->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->label(__('View')),
                \Filament\Actions\DeleteAction::make()
                    ->label(__('Delete')),
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
            ]);
    }
}
