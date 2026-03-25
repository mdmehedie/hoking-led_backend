<?php

namespace App\Filament\Admin\Resources\CaseStudyResource\Form;

use App\Models\CaseStudy;
use App\Models\Locale;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CaseStudyForm
{
    public static function form(Schema $schema): Schema
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return $schema->schema([
            Tabs::make('Case Study Tabs')->tabs([
                Tab::make(__('General Information'))->schema([
                    TextInput::make('slug')
                        ->unique(ignoreRecord: true)
                        ->required()
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
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'review' => 'Review',
                            'published' => 'Published',
                        ])
                        ->required(),
                    Hidden::make('published_at')
                        ->default(now()),
                    Hidden::make('author_id')
                        ->default(auth()->id()),
                ]),
                Tab::make(__('Translations'))->schema([
                    Tabs::make('Language Tabs')->tabs(
                        collect($activeLocales)->map(function (string $locale) use ($defaultLocale) {
                            $isDefault = $locale === $defaultLocale;

                            return Tab::make(strtoupper($locale))
                                ->schema([
                                    TextInput::make("title.{$locale}")
                                        ->label(__('Title'))
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isDefault) {
                                            if (!$isDefault) {
                                                return;
                                            }

                                            if (blank($get('slug'))) {
                                                $set('slug', static::generateUniqueSlug($state, null));
                                            }
                                        })
                                        ->live()
                                        ->required($isDefault),
                                    Textarea::make("excerpt.{$locale}")
                                        ->label(__('Excerpt')),
                                    \App\Filament\Forms\Components\CustomRichEditor::make("content.{$locale}")
                                        ->label(__('Content'))
                                        ->required($isDefault),
                                    FileUpload::make("image_path.{$locale}")
                                        ->label(__('Image'))
                                        ->image()
                                        ->disk('public')
                                        ->directory('case-studies')
                                        ->visibility('public')
                                        ->imageEditor()
                                        ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                                ]);
                        })->all()
                    ),
                ]),
                Tab::make('SEO')->schema([
                    TextInput::make('meta_title'),
                    Textarea::make('meta_description'),
                    Textarea::make('meta_keywords'),
                    TextInput::make('canonical_url'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'case_studies';
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
