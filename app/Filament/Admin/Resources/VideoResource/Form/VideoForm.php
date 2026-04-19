<?php

namespace App\Filament\Admin\Resources\VideoResource\Form;

use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class VideoForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Video Tabs')->tabs([
                Tab::make(__('General Information'))->schema(self::generalSchema()),
                Tab::make(__('Media'))->schema(self::mediaSchema()),
            ])->columnSpanFull(),
        ]);
    }

    private static function generalSchema(): array
    {
        return [
            TextInput::make('slug')
                ->label(__('Slug'))
                ->readOnly()
                ->required(),
            TextInput::make("video_url")
                ->label(__('Video URL'))
                ->url()
                ->maxLength(255),
        ];
    }

    private static function mediaSchema(): array
    {
        $keepOriginal = fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName();

        return [
            FileUpload::make('video_path')
                ->label(__('Video File'))
                ->disk('public')
                ->directory('videos/files')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal)
                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                ->maxSize(102400), // 100MB

            FileUpload::make('thumbnail_path')
                ->label(__('Thumbnail'))
                ->image()
                ->disk('public')
                ->directory('videos/thumbnails')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing($keepOriginal),
        ];
    }
}
