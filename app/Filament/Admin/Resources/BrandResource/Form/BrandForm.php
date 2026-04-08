<?php

namespace App\Filament\Admin\Resources\BrandResource\Form;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class BrandForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                Hidden::make('slug'),
                TextInput::make('website_url')
                    ->label(__('Website URL'))
                    ->url()
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(__('Is Active'))
                    ->default(true),
                FileUpload::make('logo')
                    ->label(__('Logo'))
                    ->image()
                    ->disk('public')
                    ->directory('brands/logos')
                    ->visibility('public')
                    ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName())
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
