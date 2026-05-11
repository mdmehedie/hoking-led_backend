<?php

namespace App\Filament\Admin\Resources\BrandResource\Table;

use App\Models\Brand;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BrandTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->square()
                    ->size(40)
                    ->disk('public'),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('website_url')
                    ->label(__('Website'))
                    ->url(fn ($state) => $state, true)
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Active')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('update', $record)),
                DeleteAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->can('delete', new Brand())),
            ]);
    }
}
