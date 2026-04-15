<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SuccessfulProjectResource\Table\SuccessfulProjectTable;
use App\Filament\Admin\Resources\SuccessfulProjectResource\Pages;
use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class SuccessfulProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Successful Projects');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Projects';
    }

    public static function canViewAny(): bool
    {
        return false;
//        return auth()->user()->can('view any project');
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
        return SuccessfulProjectTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuccessfulProjects::route('/'),
        ];
    }
}
