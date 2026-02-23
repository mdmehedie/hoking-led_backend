<?php

namespace App\Filament\Admin\Resources\Sliders\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class SlidersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->formatStateUsing(fn ($state) => strip_tags($state)),
                TextColumn::make('media_type')->label('Media Type')->formatStateUsing(fn ($state) => match($state) {
                    'image' => 'Image',
                    'gif' => 'GIF (Playable)',
                    'video_url' => 'Video URL',
                    'video_file' => 'Uploaded Video',
                    default => $state,
                }),
                ImageColumn::make('image_path')->label('Media'),
                BooleanColumn::make('status'),
                TextColumn::make('order')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
                                '0' => 'Inactive',
                                '1' => 'Active',
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each->update(['status' => $data['status']]);
                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body('Selected items have been updated to ' . ($data['status'] ? 'Active' : 'Inactive') . '.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-cog'),
            ]);
    }
}
