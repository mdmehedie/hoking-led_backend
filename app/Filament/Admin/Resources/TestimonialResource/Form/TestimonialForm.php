<?php

namespace App\Filament\Admin\Resources\TestimonialResource\Form;

use App\Models\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;

class TestimonialForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Testimonial Tabs')->tabs([
                Tab::make(__('General Information'))->schema([
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
                    Toggle::make('is_visible')
                        ->label(__('Visible'))
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label(__('Sort Order'))
                        ->numeric()
                        ->default(0),
                ])->columns(3),

                Tab::make(__('Translations'))->schema(self::translationTabsSchema()),

                Tab::make(__('Media'))->schema([
                    FileUpload::make('image_path')
                        ->label(__('Client Photo'))
                        ->image()
                        ->disk('public')
                        ->directory('testimonials')
                        ->visibility('public')
                        ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName()),
                ]),

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

    private static function translationTabsSchema(): array
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return [
            Tabs::make('Language Tabs')->tabs(
                collect($activeLocales)->map(fn (string $locale) => self::localeTabSchema($locale, $locale === $defaultLocale))->all()
            ),
        ];
    }

    private static function localeTabSchema(string $locale, bool $isDefault): Tab
    {
        return Tab::make(strtoupper($locale))
            ->schema([
                TextInput::make("client_name.{$locale}")
                    ->label(__('Client Name'))
                    ->required($isDefault)
                    ->maxLength(255),
                TextInput::make("client_position.{$locale}")
                    ->label(__('Client Position'))
                    ->maxLength(255),
                TextInput::make("client_company.{$locale}")
                    ->label(__('Client Company'))
                    ->maxLength(255),
                Textarea::make("testimonial.{$locale}")
                    ->label(__('Testimonial'))
                    ->required($isDefault)
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
