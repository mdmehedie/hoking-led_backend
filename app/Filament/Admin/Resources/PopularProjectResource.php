<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PopularProjectResource\Table\PopularProjectTable;
use App\Filament\Admin\Resources\PopularProjectResource\Pages;
use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PopularProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-fire';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Popular Projects');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Projects';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view any project');
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
        return auth()->user()->can('view project');
    }

    public static function table(Table $table): Table
    {
        return PopularProjectTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPopularProjects::route('/'),
        ];
    }
}
