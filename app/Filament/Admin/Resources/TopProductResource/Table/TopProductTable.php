<?php

namespace App\Filament\Admin\Resources\TopProductResource\Table;

use App\Filament\Admin\Resources\TopProductResource;
use App\Filament\Admin\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TopProductTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Product::where('is_top', true))
            ->headerActions([
                Action::make('addToTop')
                    ->label(__('Add to Top'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Select::make('product_id')
                            ->label(__('Product'))
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Product::where('is_top', false)
                                    ->pluck('title', 'id');
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return Product::where('is_top', false)
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
                            $product->update(['is_top' => true]);
                            Notification::make()
                                ->success()
                                ->title(__('Added'))
                                ->body(__('Product added to top products.'))
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                        'archived' => __('Archived'),
                    ]),
                SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->options(Category::pluck('name', 'id')),
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn ($record) => ProductResource::getUrl('edit', ['record' => $record])),
                Action::make('remove')
                    ->label(__('Remove from Top'))
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Remove from Top Products'))
                    ->modalDescription(__('Are you sure you want to remove this product from the top products list?'))
                    ->action(function ($record) {
                        $record->update(['is_top' => false]);
                        Notification::make()
                            ->success()
                            ->title(__('Removed'))
                            ->body(__('Product removed from top products.'))
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('removeFromTop')
                    ->label(__('Remove from Top'))
                    ->color('danger')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each->update(['is_top' => false]);
                        Notification::make()
                            ->success()
                            ->title(__('Removed'))
                            ->body($count . ' ' . __('products removed from top products.'))
                            ->send();
                    }),
            ]);
    }
}
