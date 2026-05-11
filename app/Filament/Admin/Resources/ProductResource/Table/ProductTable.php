<?php

namespace App\Filament\Admin\Resources\ProductResource\Table;

use App\Filament\Admin\Resources\ProductResource;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProductTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('main_image')
                    ->label(__('Image'))
                    ->disk('public'),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                        'archived' => __('Archived'),
                    ])
                    ->rules(['required'])
                    ->sortable()
                    ->disabled(fn ($record) => ! ProductResource::canEdit($record))
                    ->afterStateUpdated(function ($state, $record) {
                        Notification::make()
                            ->success()
                            ->title(__('Status updated'))
                            ->body(__('Product status has been changed to') . ' ' . $state . '.')
                            ->send();
                    }),
                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                        'archived' => __('Archived'),
                    ]),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
            ])
            ->actions([
//                Action::make('share')
//                    ->label(__('Share'))
//                    ->icon('heroicon-o-share')
//                    ->color('success')
//                    ->visible(fn ($record) => $record->status === 'published')
//                    ->form([
//                        Section::make(__('URL Preview'))
//                            ->description(__('This is the URL that will be included in your social media posts'))
//                            ->schema([
//                                TextInput::make('url_preview')
//                                    ->label(__('Content URL'))
//                                    ->default(fn ($record) => ProductResource::generateShareUrl($record, 'product'))
//                                    ->disabled()
//                                    ->helperText(__('This URL will be shared on the selected social media platforms')),
//                            ]),
//                        CheckboxList::make('platforms')
//                            ->label(__('Share to Platforms'))
//                            ->options([
//                                'facebook' => __('Facebook'),
//                                'twitter' => __('Twitter (X)'),
//                                'linkedin' => __('LinkedIn'),
//                            ])
//                            ->default(['facebook', 'twitter', 'linkedin'])
//                            ->required()
//                            ->helperText(__('Select which social media platforms to share this product on')),
//                    ])
//                    ->action(function ($record, array $data) {
//                        $platforms = array_values(array_filter($data['platforms'] ?? []));
//
//                        $activeAccounts = SocialAccount::query()
//                            ->whereIn('platform', $platforms)
//                            ->where('is_active', true)
//                            ->count();
//
//                        if ($activeAccounts === 0) {
//                            Notification::make()
//                                ->danger()
//                                ->title(__('No active social accounts'))
//                                ->body(__('Please configure and activate at least one social media account for the selected platforms.'))
//                                ->send();
//                            return;
//                        }
//
//                        try {
//                            // Run now so we can give reliable success/failure feedback.
//                            \App\Jobs\PublishToSocialMedia::dispatchSync($record, 'product', $platforms);
//
//                            Notification::make()
//                                ->success()
//                                ->title(__('Shared successfully'))
//                                ->body(__('Posted to the selected platforms.'))
//                                ->send();
//                        } catch (Throwable $e) {
//                            Notification::make()
//                                ->danger()
//                                ->title(__('Share failed'))
//                                ->body($e->getMessage())
//                                ->send();
//                        }
//                    })
//                    ->modalHeading(__('Share Product'))
//                    ->modalSubmitActionLabel(__('Share Now')),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->can('delete product')),
                BulkAction::make('change_status')
                    ->label(__('Change Status'))
                    ->visible(fn () => auth()->user()->can('edit product'))
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
                        $updatableRecords = $records->filter(fn ($record) => ProductResource::canEdit($record));
                        $skippedCount = $records->count() - $updatableRecords->count();

                        $updatableRecords->each->update(['status' => $data['status']]);
                        Notification::make()
                            ->success()
                            ->title(__('Status Updated'))
                            ->body(
                                __('Selected items have been updated to') . ' ' . $data['status'] . '.' .
                                ($skippedCount > 0 ? ' ' . __('Some selected items were skipped due to insufficient permissions.') : '')
                            )
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-cog'),
            ]);
    }
}
