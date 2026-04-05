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

    public static function getNavigationLabel(): string
    {
        return __('Forms');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create form');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit form');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete form');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view form');
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
