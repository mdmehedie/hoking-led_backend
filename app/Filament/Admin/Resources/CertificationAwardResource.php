<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CertificationAwardResource\Pages;
use App\Models\CertificationAward;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificationAwardResource extends Resource
{
    protected static ?string $model = CertificationAward::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Certifications & Awards';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $context) {
                                // For create mode, auto-generate slug
                                if (empty($context['record'])) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(CertificationAward::class, 'slug', ignoreRecord: true),

                        TextInput::make('issuing_organization')
                            ->maxLength(255),

                        DatePicker::make('date_awarded')
                            ->label('Date Awarded'),

                        Textarea::make('description')
                            ->columnSpanFull(),

                        FileUpload::make('image_path')
                            ->image()
                            ->directory('certifications')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                    ])->columns(2),

                Section::make('Visibility & Ordering')
                    ->schema([
                        Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])->columns(2),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->maxLength(500),

                        Textarea::make('meta_keywords'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('issuing_organization')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_awarded')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
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
            'index' => Pages\ListCertificationAwards::route('/'),
            'create' => Pages\CreateCertificationAward::route('/create'),
            'edit' => Pages\EditCertificationAward::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return true; // Temporarily bypass permission check
    }

    public static function canCreate(): bool
    {
        return true; // Temporarily bypass permission check
    }

    public static function canEdit($record): bool
    {
        return true; // Temporarily bypass permission check
    }

    public static function canDelete($record): bool
    {
        return true; // Temporarily bypass permission check
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'certification_awards';
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (\Illuminate\Support\Facades\DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
