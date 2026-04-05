<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TranslationResource\Form\TranslationForm;
use App\Filament\Admin\Resources\TranslationResource\Table\TranslationTable;
use App\Filament\Admin\Resources\TranslationResource\Pages;
use App\Models\UiTranslation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

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
        return TranslationForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return TranslationTable::table($table);
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
