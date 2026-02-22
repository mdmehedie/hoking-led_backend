<?php

namespace App\Filament\Admin\Resources\Sliders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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
                    RichEditor::make('title')
                        ->required(),
                    RichEditor::make('description'),
                    FileUpload::make('image_path')
                        ->directory('sliders')
                        ->visibility('public')
                        ->image()
                        ->imageResizeTargetWidth(1920)
                        ->imageResizeTargetHeight(1080)
                        ->imageCropAspectRatio('16:9'),
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
