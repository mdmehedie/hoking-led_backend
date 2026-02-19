<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
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
                TextInput::make('title')->afterStateUpdated(function ($state, callable $set, $context) {
                    $record = $context['record'] ?? null;
                    if ($record === null) {
                        $set('slug', static::generateUniqueSlug($state, $record?->id));
                    }
                })->live()->required(),
                TextInput::make('slug')->unique(ignoreRecord: true)->required(),
                Textarea::make('short_description'),
                Select::make('category_id')->relationship('category', 'name')->nullable(),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required(),
                DateTimePicker::make('published_at'),
                Toggle::make('is_featured')->label('Featured Product'),
            ]),
            Section::make('Description')->schema([
                RichEditor::make('detailed_description'),
            ]),
            Section::make('Media')->schema([
                FileUpload::make('main_image')->image()->directory('products/main'),
                FileUpload::make('gallery')->multiple()->image()->directory('products/gallery'),
                Repeater::make('video_embeds')->schema([
                    Select::make('type')->options(['embed' => 'Embed URL', 'file' => 'Self-hosted File'])->required(),
                    TextInput::make('title')->visible(fn ($get) => $get('type') === 'embed'),
                    TextInput::make('url')->url()->rules(['regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/'])->visible(fn ($get) => $get('type') === 'embed'),
                    FileUpload::make('video_file')->visible(fn ($get) => $get('type') === 'file'),
                ]),
                FileUpload::make('downloads')->multiple()->directory('products/downloads'),
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
            Section::make('Related Products')->schema([
                Select::make('related_products')->multiple()->relationship('relatedProducts', 'title'),
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
            ImageColumn::make('main_image')->label('Image'),
            TextColumn::make('title')->searchable()->sortable(),
            SelectColumn::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->rules(['required'])->sortable()->afterStateUpdated(function ($state, $record) { \Filament\Notifications\Notification::make()->success()->title('Status updated')->body('Product status has been changed to ' . $state . '.')->send(); }),
            TextColumn::make('category.name')->label('Category')->sortable(),
            TextColumn::make('published_at')->dateTime()->sortable(),
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

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'products';
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
