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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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

    public static function getNavigationLabel(): string
    {
        return __('Certifications & Awards');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Certification & Award Tabs')->tabs([
                    Tab::make(__('Basic Information'))->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
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
                            ->label(__('Slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(CertificationAward::class, 'slug', ignoreRecord: true)
                            ->rules(['regex:/^[a-z0-9-]+$/', 'no_spaces'])
                            ->helperText(__('Only lowercase letters, numbers, and hyphens are allowed. Spaces are not permitted.'))
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Convert spaces to hyphens and ensure only valid characters
                                $slug = strtolower(str_replace(' ', '-', $state));
                                $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
                                $slug = preg_replace('/-+/', '-', $slug); // Replace multiple hyphens with single
                                $slug = trim($slug, '-'); // Remove leading/trailing hyphens
                                $set('slug', $slug);
                            })
                            ->live(debounce: 300),

                        TextInput::make('issuing_organization')
                            ->label(__('Issuing Organization'))
                            ->maxLength(255),

                        DatePicker::make('date_awarded')
                            ->label(__('Date Awarded')),

                        Textarea::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),

                        FileUpload::make('image_path')
                            ->label(__('Image'))
                            ->image()
                            ->directory('certifications')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                    ]),
                    Tab::make(__('Visibility & SEO'))->schema([
                        Toggle::make('is_visible')
                            ->label(__('Visible'))
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('Lower numbers appear first')),

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
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('issuing_organization')
                    ->label(__('Issuing Organization'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_awarded')
                    ->label(__('Date Awarded'))
                    ->date()
                    ->sortable(),

                IconColumn::make('is_visible')
                    ->label(__('Visible'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('Order'))
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
