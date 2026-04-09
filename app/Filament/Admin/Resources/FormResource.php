<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FormResource\Form\FormForm;
use App\Filament\Admin\Resources\FormResource\Table\FormTable;
use App\Filament\Admin\Resources\FormResource\Pages;
use App\Filament\Admin\Resources\FormResource\RelationManagers\WebhooksRelationManager;
use App\Models\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return FormForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return FormTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            WebhooksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
