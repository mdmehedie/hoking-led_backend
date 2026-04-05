<?php

namespace App\Filament\Admin\Resources\PageResource\Table;

use App\Filament\Admin\Resources\PageResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PageTable
{
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
                Action::make('send_to_review')
                    ->label('Send to Review')
                    ->icon('heroicon-o-arrow-right')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(fn ($record) => $record->update(['status' => 'review'])),
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status === 'review')
                    ->action(fn ($record) => $record->update(['status' => 'published', 'published_at' => now()])),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record) => $record->status === 'published')
                    ->action(fn ($record) => $record->update(['status' => 'draft'])),
                Action::make('share')
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
                                    ->default(fn ($record) => PageResource::generateShareUrl($record, 'page'))
                                    ->disabled()
                                    ->helperText('This URL will be shared on the selected social media platforms'),
                            ]),
                        CheckboxList::make('platforms')
                            ->label('Share to Platforms')
                            ->options([
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter (X)',
                                'linkedin' => 'LinkedIn',
                            ])
                            ->default(['facebook', 'twitter', 'linkedin'])
                            ->required()
                            ->helperText('Select which social media platforms to share this page on'),
                    ])
                    ->action(function ($record, array $data) {
                        \App\Jobs\PublishToSocialMedia::dispatch($record, 'page', $data['platforms']);

                        Notification::make()
                            ->title('Page shared successfully!')
                            ->body('The page has been queued for sharing to selected social media platforms.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Share Page')
                    ->modalSubmitActionLabel('Share Now'),
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn ($record) => PageResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
                Action::make('delete')
                    ->label('Delete')
                    ->action(fn ($record) => $record->delete())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
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
}
