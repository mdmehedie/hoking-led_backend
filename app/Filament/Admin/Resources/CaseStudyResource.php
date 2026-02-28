<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CaseStudyResource\Pages;
use App\Models\CaseStudy;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tab;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;

    protected static ?string $navigationLabel = 'Case Studies';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

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
        return $schema
            ->schema([
                Section::make('General')->schema([
                    TextInput::make('title')
                        ->afterStateUpdated(function ($state, callable $set, $context) {
                            $record = $context['record'] ?? null;
                            if ($record === null) {
                                $set('slug', static::generateUniqueSlug($state, $record?->id));
                            }
                        })
                        ->live()
                        ->required(),
                    TextInput::make('slug')
                        ->unique(ignoreRecord: true)
                        ->required(),
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
                Section::make('Content')->schema([
                    Textarea::make('excerpt'),
                    \App\Filament\Forms\Components\CustomRichEditor::make('content')
                        ->required(),
                ]),
                Section::make('Media')->schema([
                    FileUpload::make('image_path')
                        ->image()
                        ->directory('case-studies')
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1', '4:3', '16:9', '3:2', '2:1']),
                ]),
                Section::make('SEO')->schema([
                    TextInput::make('meta_title'),
                    Textarea::make('meta_description'),
                    Textarea::make('meta_keywords'),
                    TextInput::make('canonical_url'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit' => Pages\EditCaseStudy::route('/{record}/edit'),
        ];
    }

    protected static function generateUniqueSlug($title, $id = null)
    {
        $table = 'case_studies';
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
