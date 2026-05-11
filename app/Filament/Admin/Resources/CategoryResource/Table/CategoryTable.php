<?php

namespace App\Filament\Admin\Resources\CategoryResource\Table;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryTable
{
    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('icon')
                ->label(__('Icon'))
                ->square()
                ->size(30)
                ->disk('public'),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->searchable()->sortable(),
            TextColumn::make('parent.name')->label(__('Parent'))->sortable()->placeholder('-'),
            BooleanColumn::make('is_visible')->label(__('Visible'))->sortable(),
        ])
        ->defaultSort('name')
        ->actions([
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->bulkActions([
            DeleteBulkAction::make(),
        ]);
    }
}
