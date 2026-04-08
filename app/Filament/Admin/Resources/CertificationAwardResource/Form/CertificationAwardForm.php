<?php

namespace App\Filament\Admin\Resources\CertificationAwardResource\Form;

use App\Models\CertificationAward;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CertificationAwardForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Certification & Award Tabs')->tabs([
                    Tab::make(__('Basic Information'))->schema(self::basicSchema()),
                    Tab::make(__('Media'))->schema(self::mediaSchema()),
                    Tab::make(__('SEO'))->schema(self::seoSchema()),
                ])->columnSpanFull(),
            ]);
    }

    private static function basicSchema(): array
    {
        return [
            TextInput::make('title')
                ->label(__('Title'))
                ->required()
                ->maxLength(255)
                ->live()
                ->afterStateUpdated(function ($state, callable $set, $context) {
                    if (empty($context['record'])) {
                        $set('slug', Str::slug($state));
                    }
                }),

            TextInput::make('slug')
                ->label(__('Slug'))
                ->required()
                ->maxLength(255)
                ->unique(CertificationAward::class, 'slug', ignoreRecord: true)
                ->rules(['regex:/^[a-z0-9-]+$/', 'no_spaces'])
                ->helperText(__('Only lowercase letters, numbers, and hyphens are allowed. Spaces are not permitted.'))
                ->afterStateUpdated(function ($state, callable $set) {
                    $slug = strtolower(str_replace(' ', '-', $state));
                    $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
                    $slug = preg_replace('/-+/', '-', $slug);
                    $slug = trim($slug, '-');
                    $set('slug', $slug);
                })
                ->live(debounce: 300),

            TextInput::make('issuing_organization')
                ->label(__('Issuing Organization'))
                ->maxLength(255),

            DatePicker::make('date_awarded')
                ->label(__('Date Awarded')),

            Textarea::make('description')
                ->label(__('Description'))
                ->columnSpanFull(),

            TextInput::make('sort_order')
                ->label(__('Sort Order'))
                ->numeric()
                ->default(0)
                ->helperText(__('Lower numbers appear first')),

            Toggle::make('is_visible')
                ->label(__('Visible'))
                ->default(true),
        ];
    }

    private static function mediaSchema(): array
    {
        return [
            FileUpload::make('image_path')
                ->label(__('Image'))
                ->image()
                ->disk('public')
                ->directory('certifications')
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->getUploadedFileNameForStorageUsing(fn (UploadedFile $file) => time() . '_' . $file->getClientOriginalName()),
        ];
    }

    private static function seoSchema(): array
    {
        return [
            TextInput::make('meta_title')
                ->label(__('Meta Title'))
                ->maxLength(255),

            Textarea::make('meta_description')
                ->label(__('Meta Description'))
                ->maxLength(500)
                ->columnSpanFull(),

            Textarea::make('meta_keywords')
                ->label(__('Meta Keywords'))
                ->columnSpanFull(),
        ];
    }
}
