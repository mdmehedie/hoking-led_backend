<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AppSettingResource\Form\AppSettingForm;
use App\Filament\Admin\Resources\AppSettingResource\Table\AppSettingTable;
use App\Filament\Admin\Resources\AppSettingResource\Pages;
use App\Models\AppSetting;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppSettingResource extends Resource
{
    protected static ?string $model = AppSetting::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cog;

    protected static ?string $navigationLabel = 'App Settings';

    public static function getNavigationLabel(): string
    {
        return __('App Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create appsetting');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit appsetting');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete appsetting');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }

    public static function form(Schema $schema): Schema
    {
        return AppSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppSettingTable::configure($table);
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
            'index' => Pages\ListAppSettings::route('/'),
            'create' => Pages\CreateAppSetting::route('/create'),
            'edit' => Pages\EditAppSetting::route('/{record}/edit'),
        ];
    }
}
