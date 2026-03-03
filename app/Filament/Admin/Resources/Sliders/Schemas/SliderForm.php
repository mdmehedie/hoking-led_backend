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
                Section::make(__('Slider Details'))->schema([
                    \App\Filament\Forms\Components\CustomRichEditor::make('title')
                        ->label(__('Title'))
                        ->required(),
                    \App\Filament\Forms\Components\CustomRichEditor::make('description')
                        ->label(__('Description')),
                    Select::make('media_type')
                        ->label(__('Media Type'))
                        ->options([
                            'image' => __('Image'),
                            'gif' => __('GIF (Playable)'),
                            'video_url' => __('Video URL'),
                            'video_file' => __('Uploaded Video'),
                        ])
                        ->default('image')
                        ->required()
                        ->live(),
                    FileUpload::make('image_path')
                        ->label(__('Image'))
                        ->directory('sliders')
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9', '4:3', '1:1', '3:2', '2:1'])
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->visible(fn ($get) => in_array($get('media_type'), ['image', 'gif'])),
                    TextInput::make('video_url')
                        ->label(__('Video URL'))
                        ->rules(['regex:/^((http|https):\/\/)?[\w.-]+\.[a-zA-Z]{2,}(\/\S*)?$/'])
                        ->helperText(__('Enter full URL including http:// or https://, or just the domain like youtube.com'))
                        ->visible(fn ($get) => $get('media_type') === 'video_url'),
                    FileUpload::make('video_file')
                        ->label(__('Video File'))
                        ->directory('sliders/videos')
                        ->visibility('public')
                        ->acceptedFileTypes(['video/mp4', 'video/avi', 'video/mov', 'video/webm'])
                        ->visible(fn ($get) => $get('media_type') === 'video_file'),
                    TextInput::make('link')
                        ->label(__('Link'))
                        ->extraAttributes(fn ($get) => ['class' => $get('custom_styles.link_class') ?? '']),
                    TextInput::make('alt_text')
                        ->label(__('Alt Text'))
                        ->extraAttributes(fn ($get) => ['class' => $get('custom_styles.alt_text_class') ?? '']),
                    TextInput::make('order')
                        ->label(__('Order'))
                        ->numeric()
                        ->default(0),
                    Toggle::make('status')
                        ->label(__('Status'))
                        ->default(true),
                ]),
                Section::make(__('Custom Styling'))->schema([
                    TextInput::make('custom_styles.link_class')
                        ->label(__('Link CSS Class')),
                    TextInput::make('custom_styles.alt_text_class')
                        ->label(__('Alt Text CSS Class')),
                ]),
            ]);
    }
}
