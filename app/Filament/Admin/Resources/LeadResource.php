<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LeadResource\Pages;
use App\Filament\Admin\Resources\LeadResource\Table\LeadTable;
use App\Models\Lead;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('Leads');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Marketing');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create lead');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit lead');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete lead');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view lead');
    }

    public static function table(Table $table): Table
    {
        return LeadTable::table($table);
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
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}
