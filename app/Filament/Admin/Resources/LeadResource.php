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


    protected static ?int $navigationSort = 1;

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
