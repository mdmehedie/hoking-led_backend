<?php

namespace App\Filament\Admin\Resources\AnalyticsEventResource\Table;

use App\Models\AnalyticsEvent;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnalyticsEventTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('page')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('url')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (AnalyticsEvent $record): string => $record->url),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_agent_info.browser')
                    ->label('Browser')
                    ->sortable(),
                TextColumn::make('user_agent_info.is_mobile')
                    ->label('Mobile')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->sortable(),
                TextColumn::make('formatted_parameters')
                    ->label('Parameters')
                    ->limit(50)
                    ->tooltip(fn (AnalyticsEvent $record): string => $record->formatted_parameters),
                TextColumn::make('event_time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event_name')
                    ->options(function () {
                        return AnalyticsEvent::distinct('event_name')
                            ->pluck('event_name', 'event_name')
                            ->toArray();
                    }),
                SelectFilter::make('page')
                    ->options(function () {
                        return AnalyticsEvent::distinct('page')
                            ->whereNotNull('page')
                            ->pluck('page', 'page')
                            ->toArray();
                    }),
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable(),
                Filter::make('event_time')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('From Date'),
                        DatePicker::make('end_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query) => $query->whereDate('event_time', '>=', $data['start_date'])
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query) => $query->whereDate('event_time', '<=', $data['end_date'])
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_time', 'desc');
    }
}
