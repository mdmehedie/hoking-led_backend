<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuthorResource\Pages;
use App\Models\Author;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationLabel = 'Authors';

    public static function getNavigationLabel(): string
    {
        return __('Authors');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    public static function canCreate(): bool
    {
        return auth()->user()->can('create author');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit author');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete author');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view author');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Author Tabs')->tabs([
                Tab::make(__('Author Information'))->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('bio')
                        ->rows(4),
                ]),
                Tab::make(__('Profile Image'))->schema([
                    Forms\Components\FileUpload::make('avatar')
                        ->image()
                        ->directory('authors')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1']),
                ]),
                Tab::make(__('Social Links'))->schema([
                    Forms\Components\Repeater::make('social_links')
                        ->schema([
                            Forms\Components\Select::make('platform')
                                ->options([
                                    'facebook' => 'Facebook',
                                    'twitter' => 'Twitter / X',
                                    'linkedin' => 'LinkedIn',
                                    'instagram' => 'Instagram',
                                    'youtube' => 'YouTube',
                                    'tiktok' => 'TikTok',
                                    'github' => 'GitHub',
                                    'website' => 'Website',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('url')
                                ->rules(['regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i'])
                                ->required(),
                        ])
                        ->default([]),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bio')
                    ->limit(50),
                Tables\Columns\ImageColumn::make('avatar'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
