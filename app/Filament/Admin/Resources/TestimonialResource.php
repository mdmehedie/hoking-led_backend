<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput as FormTextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
        return $schema
            ->schema([
                Tabs::make('Testimonial Tabs')->tabs([
                    Tab::make(__('Client Information'))->schema([
                        TextInput::make('client_name')
                            ->label(__('Client Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('client_position')
                            ->label(__('Client Position'))
                            ->maxLength(255),
                        TextInput::make('client_company')
                            ->label(__('Client Company'))
                            ->maxLength(255),
                    ])->columns(2),

                    Tab::make(__('Testimonial Content'))->schema([
                        Textarea::make('testimonial')
                            ->label(__('Testimonial'))
                            ->required()
                            ->columnSpanFull(),
                        Select::make('rating')
                            ->label(__('Rating'))
                            ->options([
                                1 => '⭐ ' . __('(1 star)'),
                                2 => '⭐⭐ ' . __('(2 stars)'),
                                3 => '⭐⭐⭐ ' . __('(3 stars)'),
                                4 => '⭐⭐⭐⭐ ' . __('(4 stars)'),
                                5 => '⭐⭐⭐⭐⭐ ' . __('(5 stars)'),
                            ])
                            ->default(5)
                            ->required(),
                    ]),

                    Tab::make(__('Media'))->schema([
                        FileUpload::make('image_path')
                            ->label(__('Image'))
                            ->image()
                            ->directory('testimonials')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                                '16:9',
                            ]),
                    ]),

                    Tab::make(__('Visibility & Ordering'))->schema([
                        Toggle::make('is_visible')
                            ->label(__('Visible'))
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])->columns(2),

                    Tab::make(__('SEO'))->schema([
                        TextInput::make('meta_title')
                            ->label(__('Meta Title'))
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->label(__('Meta Description'))
                            ->maxLength(500),
                        Textarea::make('meta_keywords')
                            ->label(__('Meta Keywords')),
                    ]),
                ])->columnSpanFull(),
            ]);
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
