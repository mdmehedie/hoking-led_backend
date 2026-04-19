<?php

namespace App\Filament\Admin\Resources\PageResource\Table;

use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PageTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label(__('Image'))
                    ->square()
                    ->size(40),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label(__('Author'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => __('Draft'),
                        'review' => __('Review'),
                        'published' => __('Published'),
                    ]),
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn ($record) => \App\Filament\Admin\Resources\PageResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
