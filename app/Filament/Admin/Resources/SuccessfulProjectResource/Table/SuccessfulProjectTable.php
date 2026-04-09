<?php

namespace App\Filament\Admin\Resources\SuccessfulProjectResource\Table;

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuccessfulProjectTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Project::where('is_successful', true))
            ->headerActions([
                Action::make('addSuccessful')
                    ->label(__('Add to Successful'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Select::make('project_id')
                            ->label(__('Project'))
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Project::where('is_successful', false)
                                    ->pluck('title', 'id');
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return Project::where('is_successful', false)
                                    ->where('title', 'like', "%{$search}%")
                                    ->pluck('title', 'id');
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                return Project::find($value)?->title;
                            }),
                    ])
                    ->action(function (array $data) {
                        $project = Project::find($data['project_id']);
                        if ($project) {
                            $project->update(['is_successful' => true]);
                            Notification::make()
                                ->success()
                                ->title(__('Added'))
                                ->body(__('Project added to successful projects.'))
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn ($record) => ProjectResource::getUrl('edit', ['record' => $record])),
                Action::make('remove')
                    ->label(__('Remove from Successful'))
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Remove from Successful Projects'))
                    ->modalDescription(__('Are you sure you want to remove this project from the successful projects list?'))
                    ->action(function ($record) {
                        $record->update(['is_successful' => false]);
                        Notification::make()
                            ->success()
                            ->title(__('Removed'))
                            ->body(__('Project removed from successful projects.'))
                            ->send();
                    }),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
