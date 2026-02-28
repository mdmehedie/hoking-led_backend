<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuthorResource\Pages;
use App\Models\Author;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
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

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    public static function canAccessResource(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Author Information')->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('bio')
                    ->rows(4),
                Section::make('Profile Image')->schema([
                    Forms\Components\FileUpload::make('avatar')
                        ->image()
                        ->directory('authors')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1']),
                ]),
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
