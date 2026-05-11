<?php

namespace App\Filament\Admin\Resources\CertificationAwardResource\Table;

use App\Models\CertificationAward;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertificationAwardTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('issuing_organization')
                    ->label(__('Issuing Organization'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date_awarded')
                    ->label(__('Date Awarded'))
                    ->date()
                    ->sortable(),

                IconColumn::make('is_visible')
                    ->label(__('Visible'))
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label(__('Order'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('update', $record)),
                DeleteAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('delete', $record)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->can('delete', new CertificationAward())),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
