<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsResource\Form\NewsForm;
use App\Filament\Admin\Resources\NewsResource\Table\NewsTable;
use App\Filament\Admin\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'News';

    protected static ?string $slug = 'news-items';


    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view news');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Posts';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create news');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit news');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete news');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view news');
    }

    public static function form(Schema $schema): Schema
    {
        return NewsForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
