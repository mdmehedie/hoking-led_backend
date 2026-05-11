<?php

namespace App\Filament\Admin\Resources\ProjectResource\Table;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label(__('Cover'))
                    ->square()
                    ->size(40)
                    ->disk('public'),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_popular')
                    ->label(__('Popular'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_successful')
                    ->label(__('Successful'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                        'archived' => __('Archived'),
                    ]),
                TernaryFilter::make('is_featured')
                    ->label(__('Featured')),
                TernaryFilter::make('is_popular')
                    ->label(__('Popular')),
                TernaryFilter::make('is_successful')
                    ->label(__('Successful')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
