<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LanguageResource\Form\LanguageForm;
use App\Filament\Admin\Resources\LanguageResource\Table\LanguageTable;
use App\Filament\Admin\Resources\LanguageResource\Pages;
use App\Models\Locale;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

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
        return LanguageForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return LanguageTable::table($table);
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
