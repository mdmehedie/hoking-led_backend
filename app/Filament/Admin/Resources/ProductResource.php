<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cube;

    protected static ?string $navigationLabel = 'Products';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('General')->schema([
                TextInput::make('title')->required(),
                Textarea::make('short_description'),
                Select::make('category_id')->relationship('category', 'name')->nullable(),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required(),
                DateTimePicker::make('published_at'),
            ]),
            Section::make('Description')->schema([
                RichEditor::make('detailed_description'),
            ]),
            Section::make('Technical Specs')->schema([
                Repeater::make('technical_specs')->schema([
                    TextInput::make('key')->required(),
                    TextInput::make('value')->required(),
                ]),
            ]),
            Section::make('Tags')->schema([
                Repeater::make('tags')->schema([
                    TextInput::make('tag')->required(),
                ]),
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
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\SelectColumn::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->sortable(),
            Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
            Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
            Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
