<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SliderResource\Form\SliderForm;
use App\Filament\Admin\Resources\SliderResource\Table\SliderTable;
use App\Filament\Admin\Resources\SliderResource\Pages;
use App\Models\Slider;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Sliders';


    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create slider');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit slider');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete slider');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view slider');
    }

    public static function form(Schema $schema): Schema
    {
        return SliderForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return SliderTable::table($table);
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
