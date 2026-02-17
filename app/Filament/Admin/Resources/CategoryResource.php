<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Folder;

    protected static ?string $navigationLabel = 'Categories';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('General')->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')->unique(ignoreRecord: true)->required(),
                Textarea::make('description'),
                Select::make('parent_id')->relationship('parent', 'name')->nullable(),
                Toggle::make('is_visible')->default(true),
            ]),
            Section::make('SEO')->schema([
                TextInput::make('meta_title'),
                Textarea::make('meta_description'),
                Textarea::make('meta_keywords'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('parent.name')->label('Parent')->sortable(),
            Tables\Columns\BooleanColumn::make('is_visible')->sortable(),
        ])->actions([
            Action::make('edit')
                ->url(fn ($record) => static::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-o-pencil'),
            Action::make('delete')
                ->action(fn ($record) => $record->delete())
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ]);
    }

    public static function canAccessResource(): bool
    {
        return auth()->user()->hasRole('Super Admin');
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
