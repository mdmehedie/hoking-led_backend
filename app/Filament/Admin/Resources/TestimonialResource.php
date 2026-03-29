<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TestimonialResource\Form\TestimonialForm;
use App\Filament\Admin\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_name')
                    ->label(__('Client Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_position')
                    ->label(__('Client Position'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_company')
                    ->label(__('Client Company'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(fn (string $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('testimonial')
                    ->label(__('Testimonial'))
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label(__('Visible'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
