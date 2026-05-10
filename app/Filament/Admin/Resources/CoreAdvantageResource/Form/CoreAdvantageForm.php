<?php

namespace App\Filament\Admin\Resources\CoreAdvantageResource\Form;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;

class CoreAdvantageForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Core Advantage Tabs')->tabs([
                Tab::make(__('Core Advantage Content'))->schema([
                    TextInput::make('title')
                        ->label(__('Title'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->label(__('Description'))
                        ->required()
                        ->columnSpanFull(),
                    FileUpload::make('icon')
                        ->label(__('Icon Image'))
                        ->image()
                        ->disk('public')
                        ->directory('core-advantages/icons')
                        ->visibility('public')
                        ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName())
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label(__('Active'))
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label(__('Sort Order'))
                        ->numeric()
                        ->default(0),
                ])->columns(2),
            ])->columnSpanFull(),
        ]);
    }
}
