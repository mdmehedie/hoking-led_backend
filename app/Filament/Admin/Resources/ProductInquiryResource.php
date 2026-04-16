<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductInquiryResource\Pages;
use App\Filament\Admin\Resources\ProductInquiryResource\Table\ProductInquiryTable;
use App\Models\ContactSubmission;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductInquiryResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Product Inquiry';

    protected static ?string $slug = 'product-inquiry';


    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Marketing';
    }

    public static function table(Table $table): Table
    {
        return ProductInquiryTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductInquiry::route('/'),
            'view' => Pages\ViewProductInquiry::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('source', 'product_page');
    }
}
