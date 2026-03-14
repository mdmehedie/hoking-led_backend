<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnalyticsEventResource\Pages;
use App\Models\AnalyticsEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class AnalyticsEventResource extends Resource
{
    protected static ?string $model = AnalyticsEvent::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function getNavigationGroup(): ?string
    {
        return 'Analytics';
    }

    public static function getNavigationLabel(): string
    {
        return 'Events';
    }

    public static function getModelLabel(): string
    {
        return 'Event';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Events';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Analytics Event Tabs')->tabs([
                    Tab::make(__('Event Information'))->schema([
                        Forms\Components\TextInput::make('event_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('page')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('url')
                            ->maxLength(500),
                        Forms\Components\DateTimePicker::make('event_time')
                            ->required(),
                    ]),
                    Tab::make(__('Technical Details'))->schema([
                        Forms\Components\Textarea::make('user_agent')
                            ->rows(3),
                        Forms\Components\KeyValue::make('parameters')
                            ->label('Event Parameters')
                            ->keyLabel('Parameter')
                            ->valueLabel('Value')
                            ->addable()
                            ->deletable(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->nullable(),
                    ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('page')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (AnalyticsEvent $record): string => $record->url),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent_info.browser')
                    ->label('Browser')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent_info.is_mobile')
                    ->label('Mobile')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('formatted_parameters')
                    ->label('Parameters')
                    ->limit(50)
                    ->tooltip(fn (AnalyticsEvent $record): string => $record->formatted_parameters),
                Tables\Columns\TextColumn::make('event_time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_name')
                    ->options(function () {
                        return AnalyticsEvent::distinct('event_name')
                            ->pluck('event_name', 'event_name')
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('page')
                    ->options(function () {
                        return AnalyticsEvent::distinct('page')
                            ->whereNotNull('page')
                            ->pluck('page', 'page')
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable(),
                Tables\Filters\Filter::make('event_time')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('end_date')
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
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_time', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyticsEvents::route('/'),
            'view' => Pages\ViewAnalyticsEvent::route('/{record}'),
        ];
    }
}
