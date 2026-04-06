<?php

namespace App\Filament\Admin\Resources\AuthorResource\Form;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
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
}
