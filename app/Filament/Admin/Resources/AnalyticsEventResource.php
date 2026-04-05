<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnalyticsEventResource\Form\AnalyticsEventForm;
use App\Filament\Admin\Resources\AnalyticsEventResource\Table\AnalyticsEventTable;
use App\Filament\Admin\Resources\AnalyticsEventResource\Pages;
use App\Models\AnalyticsEvent;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AnalyticsEventResource extends Resource
{
    protected static ?string $model = AnalyticsEvent::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function getNavigationGroup(): ?string
    {
        return 'Analytics';
    }

    public static function getNavigationLabel(): string
    {
        return 'Events';
    }

    public static function getModelLabel(): string
    {
        return 'Event';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Events';
    }

    public static function form(Schema $schema): Schema
    {
        return AnalyticsEventForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return AnalyticsEventTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyticsEvents::route('/'),
            'view' => Pages\ViewAnalyticsEvent::route('/{record}'),
        ];
    }
}
