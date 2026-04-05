<?php

namespace App\Filament\Admin\Resources\RoleResource\Table;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Role Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('Permissions')),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->label(__('Edit')),
                DeleteAction::make()
                    ->label(__('Delete'))
                    ->requiresConfirmation()
                    ->modalHeading(__('Delete Role'))
                    ->modalDescription(__('Are you sure you want to delete this role? Users assigned to this role will lose their permissions.'))
                    ->modalSubmitActionLabel(__('Yes, delete it')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
