<?php

namespace App\Filament\Admin\Resources\TestimonialResource\Table;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client_name')
                    ->label(__('Client Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client_position')
                    ->label(__('Client Position'))
                    ->searchable(),
                TextColumn::make('client_company')
                    ->label(__('Client Company'))
                    ->searchable(),
                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(fn (string $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                TextColumn::make('testimonial')
                    ->label(__('Testimonial'))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                IconColumn::make('is_visible')
                    ->label(__('Visible'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
