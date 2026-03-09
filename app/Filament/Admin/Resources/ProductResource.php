<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Cube;

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $slug = 'product-items';

    public static function getNavigationLabel(): string
    {
        return __('Products');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Product Management');
    }

    // Override navigation URL to use the correct slug
    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create product');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit product');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete product');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view product');
    }

    public static function form(Schema $schema): Schema
    {
        $activeLocales = \App\Models\Locale::activeCodes();
        $defaultLocale = \App\Models\Locale::defaultCode();

        return $schema->schema([
            Section::make(__('General'))->schema([
                TextInput::make('slug')->label(__('Slug'))->unique(ignoreRecord: true)->required()->readonly(fn ($get, $record) => $record && $record->exists),
                Select::make('category_id')->relationship('category', 'name')->label(__('Category'))->nullable(),
                Select::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published'), 'archived' => __('Archived')])->required(),
                Hidden::make('published_at')
                    ->default(now()),
                Toggle::make('is_featured')->label(__('Featured Product')),
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
                                ->required(fn ($record) => !$record && $isDefault),
                            Textarea::make("short_description.{$locale}")
                                ->label(__('Short Description')),
                            \App\Filament\Forms\Components\CustomRichEditor::make("detailed_description.{$locale}")
                                ->label(__('Detailed Description'))
                                ->required(fn ($record) => !$record && $isDefault),
                        ]);
                })->all()
            ),
            Section::make(__('Media'))->schema([
                FileUpload::make('main_image')->label(__('Main Image'))->image()->directory('products/main')->imageEditor()->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                FileUpload::make('gallery')->label(__('Gallery'))->multiple()->image()->directory('products/gallery')->imageEditor()->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                Repeater::make('video_embeds')->label(__('Video Embeds'))->schema([
                    Select::make('type')->label(__('Type'))->options(['embed' => __('Embed URL'), 'file' => __('Self-hosted File')])->required(),
                    TextInput::make('title')->label(__('Title'))->visible(fn ($get) => $get('type') === 'embed'),
                    TextInput::make('url')->label(__('URL'))->url()->rules(['regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/'])->visible(fn ($get) => $get('type') === 'embed'),
                    FileUpload::make('video_file')->label(__('Video File'))->visible(fn ($get) => $get('type') === 'file'),
                ]),
                FileUpload::make('downloads')->label(__('Downloads'))->multiple()->directory('products/downloads'),
            ]),
            Section::make(__('Technical Specs'))->schema([
                Repeater::make('technical_specs')->label(__('Technical Specifications'))->schema([
                    TextInput::make('key')->label(__('Key'))->required(),
                    TextInput::make('value')->label(__('Value'))->required(),
                ]),
            ]),
            Section::make(__('Tags'))->schema([
                Repeater::make('tags')->label(__('Tags'))->schema([
                    TextInput::make('tag')->label(__('Tag'))->required(),
                ]),
            ]),
            Section::make(__('Related Products'))->schema([
                Select::make('related_products')->label(__('Related Products'))->multiple()->relationship('relatedProducts', 'title'),
            ]),
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
        return $table->columns([
            ImageColumn::make('main_image')->label(__('Image')),
            TextColumn::make('title')->label(__('Title'))->searchable()->sortable(),
            SelectColumn::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published'), 'archived' => __('Archived')])->rules(['required'])->sortable()->afterStateUpdated(function ($state, $record) { \Filament\Notifications\Notification::make()->success()->title(__('Status updated'))->body(__('Product status has been changed to') . ' ' . $state . '.')->send(); }),
            TextColumn::make('category.name')->label(__('Category'))->sortable(),
            TextColumn::make('published_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(['draft' => __('Draft'), 'published' => __('Published'), 'archived' => __('Archived')]),
            SelectFilter::make('category_id')->relationship('category', 'name'),
        ])->actions([
            Action::make('share')
                ->label(__('Share'))
                ->icon('heroicon-o-share')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'published')
                ->form([
                    Section::make(__('URL Preview'))
                        ->description(__('This is the URL that will be included in your social media posts'))
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('url_preview')
                                ->label(__('Content URL'))
                                ->default(fn ($record) => static::generateShareUrl($record, 'product'))
                                ->disabled()
                                ->helperText(__('This URL will be shared on the selected social media platforms')),
                        ]),
                    \Filament\Forms\Components\CheckboxList::make('platforms')
                        ->label(__('Share to Platforms'))
                        ->options([
                            'facebook' => __('Facebook'),
                            'twitter' => __('Twitter (X)'),
                            'linkedin' => __('LinkedIn'),
                        ])
                        ->default(['facebook', 'twitter', 'linkedin'])
                        ->required()
                        ->helperText(__('Select which social media platforms to share this product on')),
                ])
                ->action(function ($record, array $data) {
                    \App\Jobs\PublishToSocialMedia::dispatch($record, 'product', $data['platforms']);

                    \Filament\Notifications\Notification::make()
                        ->title(__('Product shared successfully!'))
                        ->body(__('The product has been queued for sharing to selected social media platforms.'))
                        ->success()
                        ->send();
                })
                ->modalHeading(__('Share Product'))
                ->modalSubmitActionLabel(__('Share Now')),
            Action::make('edit')
                ->label(__('Edit'))
                ->url(fn ($record) => static::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-o-pencil'),
            Action::make('delete')
                ->label(__('Delete'))
                ->action(fn ($record) => $record->delete())
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ])
        ->bulkActions([
            BulkAction::make('delete_selected')
                ->label(__('Delete Selected'))
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action(function (Collection $records) {
                    $count = $records->count();
                    $records->each->delete();
                    Notification::make()
                        ->success()
                        ->title(__('Deleted'))
                        ->body($count . ' ' . __('items deleted successfully.'))
                        ->send();
                }),
            BulkAction::make('change_status')
                ->label(__('Change Status'))
                ->form([
                    Select::make('status')
                        ->options([
                            'draft' => __('Draft'),
                            'published' => __('Published'),
                            'archived' => __('Archived'),
                        ])
                        ->required(),
                ])
                ->action(function (Collection $records, array $data) {
                    $records->each->update(['status' => $data['status']]);
                    Notification::make()
                        ->success()
                        ->title(__('Status Updated'))
                        ->body(__('Selected items have been updated to') . ' ' . $data['status'] . '.')
                        ->send();
                })
                ->requiresConfirmation()
                ->icon('heroicon-o-cog'),
        ]);
    }

    public static function canAccessResource(): bool
    {
        return auth()->user()->hasRole('Super Admin');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'products';
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
