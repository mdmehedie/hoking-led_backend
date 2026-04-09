<?php

namespace App\Filament\Admin\Resources\FeaturedProjectResource\Table;

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Category;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeaturedProjectTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Project::where('is_featured', true))
            ->headerActions([
                Action::make('addFeatured')
                    ->label(__('Add to Featured'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Select::make('project_id')
                            ->label(__('Project'))
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Project::where('is_featured', false)
                                    ->pluck('title', 'id');
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return Project::where('is_featured', false)
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
                            $project->update(['is_featured' => true]);
                            Notification::make()
                                ->success()
                                ->title(__('Added'))
                                ->body(__('Project added to featured projects.'))
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
                    ->label(__('Remove from Featured'))
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Remove from Featured Projects'))
                    ->modalDescription(__('Are you sure you want to remove this project from the featured projects list?'))
                    ->action(function ($record) {
                        $record->update(['is_featured' => false]);
                        Notification::make()
                            ->success()
                            ->title(__('Removed'))
                            ->body(__('Project removed from featured projects.'))
                            ->send();
                    }),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
