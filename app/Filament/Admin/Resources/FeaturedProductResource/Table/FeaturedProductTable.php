<?php

namespace App\Filament\Admin\Resources\FeaturedProductResource\Table;

use App\Filament\Admin\Resources\FeaturedProductResource;
use App\Filament\Admin\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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
            ->headerActions([
                Action::make('addFeatured')
                    ->label(__('Add to Featured'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Select::make('product_id')
                            ->label(__('Product'))
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Product::where('is_featured', false)
                                    ->pluck('title', 'id');
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return Product::where('is_featured', false)
                                    ->where('title', 'like', "%{$search}%")
                                    ->pluck('title', 'id');
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                return Product::find($value)?->title;
                            }),
                    ])
                    ->action(function (array $data) {
                        $product = Product::find($data['product_id']);
                        if ($product) {
                            $product->update(['is_featured' => true]);
                            Notification::make()
                                ->success()
                                ->title(__('Added'))
                                ->body(__('Product added to featured products.'))
                                ->send();
                        }
                    }),
            ])
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
                Action::make('edit')
                    ->label(__('Edit'))
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn ($record) => ProductResource::getUrl('edit', ['record' => $record])),
                Action::make('remove')
                    ->label(__('Remove from Featured'))
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Remove from Featured Products'))
                    ->modalDescription(__('Are you sure you want to remove this product from the featured products list?'))
                    ->action(function ($record) {
                        $record->update(['is_featured' => false]);
                        Notification::make()
                            ->success()
                            ->title(__('Removed'))
                            ->body(__('Product removed from featured products.'))
                            ->send();
                    }),
            ]);
    }
}
