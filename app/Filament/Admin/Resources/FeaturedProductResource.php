<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeaturedProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;

class FeaturedProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Star;

    protected static ?string $navigationLabel = 'Featured Products';

    protected static ?string $modelLabel = 'Featured Product';

    protected static ?string $pluralModelLabel = 'Featured Products';

    public static function table(Table $table): Table
    {
        return $table
            ->query(Product::where('is_featured', true))
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('status'),
                TextColumn::make('category.name')->label('Category'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
                \Filament\Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name'),
            ])
            ->actions([
                Action::make('remove_featured')
                    ->label('Remove from Featured')
                    ->icon('heroicon-o-x-mark')
                    ->action(function ($record) {
                        $record->update(['is_featured' => false]);
                        \Filament\Notifications\Notification::make()->success()->title('Product removed from featured')->body('The product has been removed from featured products.')->send();
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeaturedProducts::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
