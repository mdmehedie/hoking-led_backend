<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tab;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Folder;

    protected static ?string $navigationLabel = 'Categories';

    public static function getNavigationLabel(): string
    {
        return __('Categories');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Product Management');
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'categories';
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('General')->schema([
                TextInput::make('name')
                    ->afterStateUpdated(function ($state, callable $set, $context) {
                        $record = $context['record'] ?? null;
                        if ($record === null) {
                            $set('slug', static::generateUniqueSlug($state, $record?->id));
                        }
                    })
                    ->live()
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
                TextInput::make('canonical_url'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->searchable()->sortable(),
            TextColumn::make('parent.name')->label('Parent')->sortable(),
            BooleanColumn::make('is_visible')->sortable(),
        ])->actions([
            Action::make('edit')
                ->url(fn ($record) => static::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-o-pencil'),
            Action::make('delete')
                ->action(fn ($record) => $record->delete())
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ])
        ->bulkActions([
            BulkAction::make('delete_selected')
                ->label('Delete Selected')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action(function (Collection $records) {
                    $count = $records->count();
                    $records->each->delete();
                    Notification::make()
                        ->success()
                        ->title('Deleted')
                        ->body($count . ' items deleted successfully.')
                        ->send();
                }),
        ]);
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
