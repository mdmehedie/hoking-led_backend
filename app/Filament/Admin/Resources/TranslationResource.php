<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TranslationResource\Pages;
use App\Models\UiTranslation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class TranslationResource extends Resource
{
    protected static ?string $model = UiTranslation::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Language;

    protected static ?string $navigationLabel = 'Translations';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    public static function getNavigationLabel(): string
    {
        return __('Translations');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Translation Settings Tabs')->tabs([
                Tab::make(__('Translation'))->schema([
                    TextInput::make('key')
                        ->required()
                        ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                            return $rule->where('locale', $get('locale'));
                        }),
                    Select::make('locale')
                        ->required()
                        ->options(fn () => array_combine(config('app.supported_locales', ['en']), config('app.supported_locales', ['en']))),
                    Textarea::make('value')
                        ->rows(4)
                        ->nullable(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->searchable()->sortable(),
                TextColumn::make('locale')->sortable(),
                TextColumn::make('value')->limit(60)->wrap(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('locale')
                    ->options(fn () => array_combine(config('app.supported_locales', ['en']), config('app.supported_locales', ['en']))),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
