<?php

namespace App\Filament\Admin\Resources\ProductResource\Table;

use App\Filament\Admin\Resources\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
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
                    ->label(__('Image')),
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
                Action::make('share')
                    ->label(__('Share'))
                    ->icon('heroicon-o-share')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'published')
                    ->form([
                        Section::make(__('URL Preview'))
                            ->description(__('This is the URL that will be included in your social media posts'))
                            ->schema([
                                TextInput::make('url_preview')
                                    ->label(__('Content URL'))
                                    ->default(fn ($record) => ProductResource::generateShareUrl($record, 'product'))
                                    ->disabled()
                                    ->helperText(__('This URL will be shared on the selected social media platforms')),
                            ]),
                        CheckboxList::make('platforms')
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

                        Notification::make()
                            ->title(__('Product shared successfully!'))
                            ->body(__('The product has been queued for sharing to selected social media platforms.'))
                            ->success()
                            ->send();
                    })
                    ->modalHeading(__('Share Product'))
                    ->modalSubmitActionLabel(__('Share Now')),
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn ($record) => ProductResource::getUrl('edit', ['record' => $record]))
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
}
