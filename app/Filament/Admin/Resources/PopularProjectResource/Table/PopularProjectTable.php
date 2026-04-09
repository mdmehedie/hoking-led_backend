<?php

namespace App\Filament\Admin\Resources\PopularProjectResource\Table;

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PopularProjectTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Project::where('is_popular', true))
            ->headerActions([
                Action::make('addPopular')
                    ->label(__('Add to Popular'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Select::make('project_id')
                            ->label(__('Project'))
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Project::where('is_popular', false)
                                    ->pluck('title', 'id');
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return Project::where('is_popular', false)
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
                            $project->update(['is_popular' => true]);
                            Notification::make()
                                ->success()
                                ->title(__('Added'))
                                ->body(__('Project added to popular projects.'))
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
                    ->label(__('Remove from Popular'))
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Remove from Popular Projects'))
                    ->modalDescription(__('Are you sure you want to remove this project from the popular projects list?'))
                    ->action(function ($record) {
                        $record->update(['is_popular' => false]);
                        Notification::make()
                            ->success()
                            ->title(__('Removed'))
                            ->body(__('Project removed from popular projects.'))
                            ->send();
                    }),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
