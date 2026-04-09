<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CertificationAwardResource\Form\CertificationAwardForm;
use App\Filament\Admin\Resources\CertificationAwardResource\Table\CertificationAwardTable;
use App\Filament\Admin\Resources\CertificationAwardResource\Pages;
use App\Models\CertificationAward;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CertificationAwardResource extends Resource
{
    protected static ?string $model = CertificationAward::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Certifications & Awards';


    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create certificationaward');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit certificationaward');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete certificationaward');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view certificationaward');
    }

    public static function form(Schema $schema): Schema
    {
        return CertificationAwardForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificationAwardTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificationAwards::route('/'),
            'create' => Pages\CreateCertificationAward::route('/create'),
            'edit' => Pages\EditCertificationAward::route('/{record}/edit'),
        ];
    }
}
