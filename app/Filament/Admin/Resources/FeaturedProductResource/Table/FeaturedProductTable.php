<?php

namespace App\Filament\Admin\Resources\FeaturedProductResource\Table;

use App\Filament\Admin\Resources\FeaturedProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeaturedProductTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Product::where('is_featured', true))
            ->columns([
                TextColumn::make('title')->label(__('Title'))->searchable(),
                TextColumn::make('status')->label(__('Status')),
                TextColumn::make('category.name')->label(__('Category')),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published'), 'archived' => __('Archived')]),
                SelectFilter::make('category_id')->label(__('Category'))->relationship('category', 'name'),
            ])
            ->actions([
                Action::make('remove_featured')
                    ->label(__('Remove from Featured'))
                    ->icon('heroicon-o-x-mark')
                    ->action(function ($record) {
                        $record->update(['is_featured' => false]);
                        Notification::make()->success()->title(__('Product removed from featured'))->body(__('The product has been removed from featured products.'))->send();
                    })
                    ->requiresConfirmation(),
            ]);
    }
}
