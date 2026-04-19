<?php

namespace App\Filament\Admin\Resources\VideoResource\Table;

use App\Filament\Admin\Resources\VideoResource;
use App\Models\Video;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VideoTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label(__('Thumbnail'))
                    ->disk('public')
                    ->square()
                    ->size(40),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn ($record) => VideoResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
