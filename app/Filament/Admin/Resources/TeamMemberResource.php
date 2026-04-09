<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeamMemberResource\Form\TeamMemberForm;
use App\Filament\Admin\Resources\TeamMemberResource\Table\TeamMemberTable;
use App\Filament\Admin\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Team Members';

    protected static ?string $slug = 'team-members';


    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return 'Users';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create team-member');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit team-member');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete team-member');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view team-member');
    }

    public static function form(Schema $schema): Schema
    {
        return TeamMemberForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamMemberTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
