<?php

namespace App\Filament\Admin\Resources\Sliders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SliderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Slider Details')->schema([
                    \App\Filament\Forms\Components\CustomRichEditor::make('title')
                        ->required(),
                    \App\Filament\Forms\Components\CustomRichEditor::make('description'),
                    Select::make('media_type')
                        ->options([
                            'image' => 'Image',
                            'gif' => 'GIF (Playable)',
                            'video_url' => 'Video URL',
                            'video_file' => 'Uploaded Video',
                        ])
                        ->default('image')
                        ->required()
                        ->live(),
                    FileUpload::make('image_path')
                        ->directory('sliders')
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9', '4:3', '1:1', '3:2', '2:1'])
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->visible(fn ($get) => in_array($get('media_type'), ['image', 'gif'])),
                    TextInput::make('video_url')
                        ->rules(['regex:/^((http|https):\/\/)?[\w.-]+\.[a-zA-Z]{2,}(\/\S*)?$/'])
                        ->helperText('Enter full URL including http:// or https://, or just the domain like youtube.com')
                        ->visible(fn ($get) => $get('media_type') === 'video_url'),
                    FileUpload::make('video_file')
                        ->directory('sliders/videos')
                        ->visibility('public')
                        ->acceptedFileTypes(['video/mp4', 'video/avi', 'video/mov', 'video/webm'])
                        ->visible(fn ($get) => $get('media_type') === 'video_file'),
                    TextInput::make('link')
                        ->extraAttributes(fn ($get) => ['class' => $get('custom_styles.link_class') ?? '']),
                    TextInput::make('alt_text')
                        ->extraAttributes(fn ($get) => ['class' => $get('custom_styles.alt_text_class') ?? '']),
                    TextInput::make('order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('status')
                        ->default(true),
                ]),
                Section::make('Custom Styling')->schema([
                    TextInput::make('custom_styles.link_class')
                        ->label('Link CSS Class'),
                    TextInput::make('custom_styles.alt_text_class')
                        ->label('Alt Text CSS Class'),
                ]),
            ]);
    }
}
