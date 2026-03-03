<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsResource\Pages;
use App\Models\News;
use App\Models\Locale;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationLabel = 'News';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('News');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    public static function canCreate(): bool
    {
        return auth()->user()->can('create news');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit news');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete news');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view news');
    }

    public static function form(Schema $schema): Schema
    {
        $activeLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();

        return $schema
            ->schema([
                Section::make(__('General'))->schema([
                    TextInput::make('slug')
                        ->label(__('Slug'))
                        ->unique(ignoreRecord: true)
                        ->required(),
                    Select::make('status')
                        ->label(__('Status'))
                        ->options([
                            'draft' => __('Draft'),
                            'review' => __('Review'),
                            'published' => __('Published'),
                        ])
                        ->required(),
                    Hidden::make('published_at')
                        ->default(now()),
                    Hidden::make('author_id')
                        ->default(fn ($record) => $record?->author_id ?? auth()->id())
                        ->required(),
                ]),
                Tabs::make('Translations')->tabs(
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
                                    ->directory('news')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                            ]);
                    })->all()
                ),
                Section::make(__('SEO'))->schema([
                    TextInput::make('meta_title')->label(__('Meta Title')),
                    Textarea::make('meta_description')->label(__('Meta Description')),
                    Textarea::make('meta_keywords')->label(__('Meta Keywords')),
                    TextInput::make('canonical_url')->label(__('Canonical URL')),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('status'),
                TextColumn::make('author.name'),
                TextColumn::make('published_at'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Review',
                        'published' => 'Published',
                    ]),
            ])
            ->searchable()
            ->actions([
                \Filament\Actions\Action::make('send_to_review')
                    ->label('Send to Review')
                    ->icon('heroicon-o-arrow-right')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(fn ($record) => $record->update(['status' => 'review'])),
                \Filament\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status === 'review')
                    ->action(fn ($record) => $record->update(['status' => 'published', 'published_at' => now()])),
                \Filament\Actions\Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record) => $record->status === 'published')
                    ->action(fn ($record) => $record->update(['status' => 'draft'])),
                \Filament\Actions\Action::make('share')
                    ->label('Share')
                    ->icon('heroicon-o-share')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'published')
                    ->form([
                        Section::make('URL Preview')
                            ->description('This is the URL that will be included in your social media posts')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('url_preview')
                                    ->label('Content URL')
                                    ->default(fn ($record) => static::generateShareUrl($record, 'news'))
                                    ->disabled()
                                    ->helperText('This URL will be shared on the selected social media platforms'),
                            ]),
                        \Filament\Forms\Components\CheckboxList::make('platforms')
                            ->label('Share to Platforms')
                            ->options([
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter (X)',
                                'linkedin' => 'LinkedIn',
                            ])
                            ->default(['facebook', 'twitter', 'linkedin'])
                            ->required()
                            ->helperText('Select which social media platforms to share this news article on'),
                    ])
                    ->action(function ($record, array $data) {
                        \App\Jobs\PublishToSocialMedia::dispatch($record, 'news', $data['platforms']);

                        \Filament\Notifications\Notification::make()
                            ->title('News shared successfully!')
                            ->body('The news article has been queued for sharing to selected social media platforms.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Share News Article')
                    ->modalSubmitActionLabel('Share Now'),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('delete_selected')
                    ->label('Delete Selected')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()
                            ->success()
                            ->title('Deleted')
                            ->body($count . ' items deleted successfully.')
                            ->send();
                    }),
                BulkAction::make('change_status')
                    ->label('Change Status')
                    ->form([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'review' => 'Review',
                                'published' => 'Published',
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each->update(['status' => $data['status']]);
                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body('Selected items have been updated to ' . $data['status'] . '.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-cog'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'news';
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    /**
     * Generate share URL for content preview
     */
    protected static function generateShareUrl($record, string $contentType): string
    {
        // Get frontend URL from app settings or fallback to app URL
        $frontendUrl = \App\Models\AppSetting::first()?->frontend_url ?? config('app.url');

        // Ensure frontend URL doesn't end with /
        $frontendUrl = rtrim($frontendUrl, '/');

        // Get content type prefix from app settings with fallback
        $prefix = \App\Models\AppSetting::first()?->{$contentType . '_prefix'} ?? match($contentType) {
            'blog' => '/blog/',
            'news' => '/news/',
            'page' => '/pages/',
            'case_study' => '/case-studies/',
            'product' => '/products/',
            default => '/',
        };

        // Ensure prefix starts and ends with /
        $prefix = '/' . trim($prefix, '/') . '/';

        // Get slug
        $slug = $record->slug ?? '';

        // Construct full URL
        return $frontendUrl . $prefix . $slug;
    }
}
