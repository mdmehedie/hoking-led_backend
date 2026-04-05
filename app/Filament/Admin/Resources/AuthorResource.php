<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuthorResource\Form\AuthorForm;
use App\Filament\Admin\Resources\AuthorResource\Table\AuthorTable;
use App\Filament\Admin\Resources\AuthorResource\Pages;
use App\Models\Author;
use Filament\Resources\Resource;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationLabel = 'Authors';

    public static function getNavigationLabel(): string
    {
        return __('Authors');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    public static function canCreate(): bool
    {
        return auth()->user()->can('create author');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit author');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete author');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view author');
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return AuthorForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return AuthorTable::configure($table);
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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
