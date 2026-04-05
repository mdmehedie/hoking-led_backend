<?php

namespace App\Filament\Admin\Resources\SliderResource\Form;

use App\Filament\Forms\Components\CustomRichEditor;
use App\Models\Locale;
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
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return $schema
            ->schema([
                Tabs::make('Slider Content Tabs')->tabs([
                    Tab::make(__('Slider Details'))->schema([
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('Determines the display order. Lower numbers appear first.')),
                        Toggle::make('status')
                            ->label(__('Show / Hide'))
                            ->default(true)
                            ->helperText(__('Toggles the visibility of the slider on the frontend.')),
                        FileUpload::make('background_image')
                            ->label(__('Background Image'))
                            ->disk('public')
                            ->directory('sliders/backgrounds')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName()),
                        FileUpload::make('foreground_image')
                            ->label(__('Foreground Image'))
                            ->disk('public')
                            ->directory('sliders/foregrounds')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName())
                            ->helperText(__('Image displayed on the card.')),
                        TextInput::make('primary_button_link')
                            ->label(__('Primary Button Link'))
                            ->url()
                            ->helperText(__('URL the button will navigate to.')),
                    ]),
                    Tab::make(__('Translations'))->schema([
                        Tabs::make('Language Tabs')->tabs(
                            collect($activeLocales)->map(function (string $locale) use ($defaultLocale) {
                                $isDefault = $locale === $defaultLocale;

                                return Tab::make(strtoupper($locale))
                                    ->schema([
                                        TextInput::make("title.{$locale}")
                                            ->label(__('Title'))
                                            ->required($isDefault)
                                            ->helperText(__('Main heading displayed on the slider.')),
                                        CustomRichEditor::make("description.{$locale}")
                                            ->label(__('Description'))
                                            ->required($isDefault)
                                            ->helperText(__('Text displayed in the bottom-left corner of the slider.')),
                                        TextInput::make("label.{$locale}")
                                            ->label(__('Label'))
                                            ->required($isDefault)
                                            ->helperText(__('Text displayed on the slider card.')),
                                        TextInput::make("primary_button_text.{$locale}")
                                            ->label(__('Primary Button Text'))
                                            ->required($isDefault)
                                            ->helperText(__('Text for the button in the bottom-left corner of the slider.')),
                                    ])->columns(1);
                            })->all()
                        ),
                    ]),
                ])->columnSpanFull(),
            ]);
    }
}
