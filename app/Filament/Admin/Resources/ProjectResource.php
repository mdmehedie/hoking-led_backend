<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProjectResource\Form\ProjectForm;
use App\Filament\Admin\Resources\ProjectResource\Table\ProjectTable;
use App\Filament\Admin\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $slug = 'projects';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('Projects');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Project Management');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
