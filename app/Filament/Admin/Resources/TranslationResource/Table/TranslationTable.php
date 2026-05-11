<?php

namespace App\Filament\Admin\Resources\TranslationResource\Table;

use Filament\Tables\Actions\ActionGroup;

use Filament\Tables\Actions\Action;

use Filament\Tables\Actions\EditAction;

use Filament\Tables\Actions\DeleteBulkAction;

use Filament\Tables\Actions\DeleteAction;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TranslationTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->searchable()->sortable(),
                TextColumn::make('locale')->sortable(),
                TextColumn::make('value')->limit(60)->wrap(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('locale')
                    ->options(fn () => array_combine(config('app.supported_locales', ['en']), config('app.supported_locales', ['en']))),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
