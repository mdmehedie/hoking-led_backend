<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Form\ProductForm;
use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;

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
        return ProductForm::form($schema);
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
