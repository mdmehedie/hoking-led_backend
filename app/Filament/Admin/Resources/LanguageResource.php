<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LanguageResource\Pages;
use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class LanguageResource extends Resource
{
    protected static ?string $model = Locale::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Language;

    protected static ?string $navigationLabel = 'Languages';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    public static function getNavigationLabel(): string
    {
        return __('Languages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Language Settings Tabs')->tabs([
                Tab::make(__('Language Information'))->schema([
                    TextInput::make('code')
                        ->required()
                        ->maxLength(10)
                        ->unique(ignoreRecord: true),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Select::make('direction')
                        ->options([
                            'ltr' => 'LTR',
                            'rtl' => 'RTL',
                        ])
                        ->default('ltr')
                        ->required(),
                    FileUpload::make('flag_path')
                        ->label(__('Flag'))
                        ->directory('locales/flags')
                        ->image()
                        ->nullable(),
                ]),
                Tab::make(__('Settings'))->schema([
                    Toggle::make('is_active')
                        ->default(true)
                        ->required(),
                    Toggle::make('is_default')
                        ->helperText(__('Only one language can be default.'))
                        ->default(false),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('direction')->sortable(),
            BooleanColumn::make('is_default')->sortable(),
            BooleanColumn::make('is_active')->sortable(),
        ])->actions([
            \Filament\Actions\EditAction::make(),
            \Filament\Actions\DeleteAction::make(),
        ])->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
