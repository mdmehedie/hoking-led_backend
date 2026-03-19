<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CaseStudyResource\Form\CaseStudyForm;
use App\Filament\Admin\Resources\CaseStudyResource\Pages;
use App\Models\CaseStudy;
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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;

    protected static ?string $navigationLabel = 'Case Studies';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'cases';

    public static function getModelLabel(): string
    {
        return 'Case Study';
    }

    public static function getNavigationLabel(): string
    {
        return __('Case Studies');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    // Override navigation URL to use the correct slug
    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create casestudy');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit casestudy');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete casestudy');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view casestudy');
    }

    public static function form(Schema $schema): Schema
    {
        return CaseStudyForm::form($schema);
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
                                    ->default(fn ($record) => static::generateShareUrl($record, 'case_study'))
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
                            ->helperText('Select which social media platforms to share this case study on'),
                    ])
                    ->action(function ($record, array $data) {
                        \App\Jobs\PublishToSocialMedia::dispatch($record, 'case_study', $data['platforms']);

                        \Filament\Notifications\Notification::make()
                            ->title('Case study shared successfully!')
                            ->body('The case study has been queued for sharing to selected social media platforms.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Share Case Study')
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit' => Pages\EditCaseStudy::route('/{record}/edit'),
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
