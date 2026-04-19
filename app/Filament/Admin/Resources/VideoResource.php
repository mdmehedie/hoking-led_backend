<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VideoResource\Form\VideoForm;
use App\Filament\Admin\Resources\VideoResource\Table\VideoTable;
use App\Filament\Admin\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'Videos';

    protected static ?string $slug = 'videos';

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit video') || auth()->user()->hasRole('admin');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view video') || auth()->user()->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return VideoForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return VideoTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
