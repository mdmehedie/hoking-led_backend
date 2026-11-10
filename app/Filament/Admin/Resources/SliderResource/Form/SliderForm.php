<?php

namespace App\Filament\Admin\Resources\SliderResource\Form;

use App\Filament\Forms\Components\TinyEditor;
use App\Helpers\UploadFileName;
use App\Models\Locale;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Http\UploadedFile;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SliderForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Slider Content Tabs')->tabs([
                    Tab::make(__('Slider Info'))->schema([
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('Determines the display order. Lower numbers appear first.')),
                        Toggle::make('status')
                            ->label(__('Show / Hide'))
                            ->default(true)
                            ->helperText(__('Toggles the visibility of the slider on the frontend.')),
                        Select::make('background_type')
                            ->label(__('Background Type'))
                            ->options([
                                'image' => __('Image'),
                                'video' => __('Video'),
                            ])
                            ->default('image')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'image') {
                                    $set('video_url', null);
                                } else {
                                    $set('background_image', null);
                                }
                            }),
                        FileUpload::make('background_image')
                            ->label(__('Background Image'))
                            ->disk('public')
                            ->directory('sliders/backgrounds')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => UploadFileName::make($file))
                            ->helperText(__('Recommended size: 1920px width x 600px to 800px height (aspect ratio: ~3:1 or 16:9 for full desktop viewport coverage).'))
                            ->visible(fn (callable $get) => $get('background_type') === 'image')
                            ->required(fn (callable $get) => $get('background_type') === 'image'),
                        FileUpload::make('video_url')
                            ->label(__('Background Video'))
                            ->disk('public')
                            ->directory('sliders/videos')
                            ->visibility('public')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'])
                            ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => UploadFileName::make($file))
                            ->helperText(__('Recommended: MP4 format, 1920x1080 or 1920x600, under 10MB for optimal loading performance.'))
                            ->visible(fn (callable $get) => $get('background_type') === 'video')
                            ->required(fn (callable $get) => $get('background_type') === 'video'),
                        FileUpload::make('foreground_image')
                            ->label(__('Foreground Image'))
                            ->disk('public')
                            ->directory('sliders/foregrounds')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => UploadFileName::make($file))
                            ->helperText(__('Image displayed on the card. Recommended size: 223px width x 159px height (aspect ratio: ~7:5 or 1.4:1).')),
                        TextInput::make('primary_button_link')
                            ->label(__('Primary Button Link'))
                            ->url()
                            ->helperText(__('URL the button will navigate to.')),
                    ]),
                    Tab::make(__('Slider Details'))->schema([
                        TextInput::make("title")
                            ->label(__('Title'))
                            ->helperText(__('Main heading displayed on the slider.')),
                        TinyEditor::make("description")
                            ->label(__('Description'))
                            ->helperText(__('Text displayed in the bottom-left corner of the slider.')),
                        TextInput::make("label")
                            ->label(__('Label'))
                            ->helperText(__('Text displayed on the slider card.')),
                        TextInput::make("primary_button_text")
                            ->label(__('Primary Button Text'))
                            ->helperText(__('Text for the button in the bottom-left corner of the slider.')),
                    ]),
                ])->columnSpanFull(),
            ]);
    }
}
