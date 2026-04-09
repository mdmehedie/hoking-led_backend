<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogResource\Form\BlogForm;
use App\Filament\Admin\Resources\BlogResource\Table\BlogTable;
use App\Filament\Admin\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Blogs';

    protected static ?string $slug = 'blogs';


    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return 'Posts';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create blog');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit blog');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete blog');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view blog');
    }

    public static function form(Schema $schema): Schema
    {
        return BlogForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
