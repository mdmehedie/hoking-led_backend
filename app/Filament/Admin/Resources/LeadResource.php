<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;

use Filament\Support\Icons\Heroicon;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('Leads');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Marketing');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create lead');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit lead');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete lead');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view lead');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID')),
                Tables\Columns\TextColumn::make('form.name')
                    ->label(__('Form')),
                Tables\Columns\TextColumn::make('data')
                    ->label(__('Data'))
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->searchable()
            ->filters([
                SelectFilter::make('form_id')
                    ->label(__('Form'))
                    ->options(\App\Models\Form::pluck('name', 'id')),
                Filter::make('created_at')
                    ->label(__('Created Date'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('From')),
                        Forms\Components\DatePicker::make('created_until')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}
