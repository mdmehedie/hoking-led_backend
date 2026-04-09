<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PageResource\Form\PageForm;
use App\Filament\Admin\Resources\PageResource\Table\PageTable;
use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Pages';

    protected static ?string $slug = 'pages';


    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view page');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create page');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit page');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete page');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view page');
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return PageTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
