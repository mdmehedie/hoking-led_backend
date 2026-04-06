<?php

namespace App\Filament\Admin\Resources\CoreAdvantageResource\Form;

use App\Models\Locale;
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
                Tab::make(__('General Information'))->schema([
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
                ])->columns(3),

                Tab::make(__('Translations'))->schema(self::translationTabsSchema()),
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
                TextInput::make("title.{$locale}")
                    ->label(__('Title'))
                    ->required($isDefault)
                    ->maxLength(255),
                Textarea::make("description.{$locale}")
                    ->label(__('Description'))
                    ->required($isDefault)
                    ->columnSpanFull(),
            ])->columns(1);
    }
}
