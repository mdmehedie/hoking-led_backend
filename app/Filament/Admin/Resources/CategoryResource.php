<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Form\CategoryForm;
use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Filament\Admin\Resources\CategoryResource\Table\CategoryTable;
use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Folder;

    protected static ?string $navigationLabel = 'Categories';


    protected static ?int $navigationSort = 1;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return CategoryForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryTable::table($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('parent');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Products';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create category');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit category');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete category');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view category');
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
