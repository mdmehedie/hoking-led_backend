<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TestimonialResource\Form\TestimonialForm;
use App\Filament\Admin\Resources\TestimonialResource\Table\TestimonialTable;
use App\Filament\Admin\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Testimonials';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Testimonials');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function form(Schema $schema): Schema
    {
        return TestimonialForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return TestimonialTable::table($table);
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view testimonial');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create testimonial');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit testimonial');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete testimonial');
    }
}
