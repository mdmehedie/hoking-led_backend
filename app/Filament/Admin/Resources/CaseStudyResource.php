<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CaseStudyResource\Form\CaseStudyForm;
use App\Filament\Admin\Resources\CaseStudyResource\Pages;
use App\Filament\Admin\Resources\CaseStudyResource\Table\CaseStudyTable;
use App\Models\CaseStudy;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Case Studies';

    protected static ?string $slug = 'cases';


    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create casestudy');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit casestudy');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete casestudy');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view casestudy');
    }

    public static function form(Schema $schema): Schema
    {
        return CaseStudyForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return CaseStudyTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit' => Pages\EditCaseStudy::route('/{record}/edit'),
        ];
    }
}
