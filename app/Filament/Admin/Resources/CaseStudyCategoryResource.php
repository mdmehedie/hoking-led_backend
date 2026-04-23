<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CaseStudyCategoryResource\Form\CaseStudyCategoryForm;
use App\Filament\Admin\Resources\CaseStudyCategoryResource\Table\CaseStudyCategoryTable;
use App\Filament\Admin\Resources\CaseStudyCategoryResource\Pages;
use App\Models\CaseStudyCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CaseStudyCategoryResource extends Resource
{
    protected static ?string $model = CaseStudyCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?string $slug = 'case-study-categories';


    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Case Studies';
    }

    public static function form(Schema $schema): Schema
    {
        return CaseStudyCategoryForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return CaseStudyCategoryTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseStudyCategories::route('/'),
            'create' => Pages\CreateCaseStudyCategory::route('/create'),
            'edit' => Pages\EditCaseStudyCategory::route('/{record}/edit'),
        ];
    }
}
