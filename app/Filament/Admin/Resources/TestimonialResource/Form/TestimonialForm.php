<?php

namespace App\Filament\Admin\Resources\TestimonialResource\Form;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Testimonial Tabs')->tabs([
                Tab::make(__('Client Information'))->schema([
                    TextInput::make('client_name')
                        ->label(__('Client Name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('client_position')
                        ->label(__('Client Position'))
                        ->maxLength(255),
                    TextInput::make('client_company')
                        ->label(__('Client Company'))
                        ->maxLength(255),
                ])->columns(2),

                Tab::make(__('Testimonial Content'))->schema([
                    Textarea::make('testimonial')
                        ->label(__('Testimonial'))
                        ->required()
                        ->columnSpanFull(),
                    Select::make('rating')
                        ->label(__('Rating'))
                        ->options([
                            1 => '⭐ ' . __('(1 star)'),
                            2 => '⭐⭐ ' . __('(2 stars)'),
                            3 => '⭐⭐⭐ ' . __('(3 stars)'),
                            4 => '⭐⭐⭐⭐ ' . __('(4 stars)'),
                            5 => '⭐⭐⭐⭐⭐ ' . __('(5 stars)'),
                        ])
                        ->default(5)
                        ->required(),
                ]),

                Tab::make(__('Media'))->schema([
                    FileUpload::make('image_path')
                        ->label(__('Image'))
                        ->image()
                        ->disk('public')
                        ->directory('testimonials')
                        ->visibility('public')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '1:1',
                            '4:3',
                            '16:9',
                        ]),
                ]),

                Tab::make(__('Visibility & Ordering'))->schema([
                    Toggle::make('is_visible')
                        ->label(__('Visible'))
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label(__('Sort Order'))
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])->columns(2),

                Tab::make(__('SEO'))->schema([
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
}
