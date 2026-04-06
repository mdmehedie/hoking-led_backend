<?php

namespace App\Filament\Admin\Resources\CertificationAwardResource\Table;

use App\Filament\Admin\Resources\CertificationAwardResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
