<?php

namespace App\Filament\Admin\Resources\ContactSubmissionResource\Pages;

use App\Filament\Admin\Resources\ContactSubmissionResource;
use App\Models\ContactSubmission;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewContactSubmission extends ViewRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            ActionGroup::make([
                Action::make('assign')
                    ->label(__('Assign'))
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading(__('Assign Submission'))
                    ->form([
                        Select::make('assigned_to')
                            ->label(__('Assign to'))
                            ->options(fn (): array => User::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->fillForm(fn (Model $record): array => [
                        'assigned_to' => $record->assigned_to,
                    ])
                    ->action(fn (Model $record, array $data) => $record->update(['assigned_to' => $data['assigned_to']])),

                Action::make('set_status')
                    ->label(__('Change Status'))
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->modalHeading(__('Change Status'))
                    ->form([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'new' => __('New'),
                                'in_progress' => __('In Progress'),
                                'resolved' => __('Resolved'),
                                'closed' => __('Closed'),
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn (Model $record): array => [
                        'status' => $record->status,
                    ])
                    ->action(function (Model $record, array $data): void {
                        /** @var ContactSubmission $record */
                        match ($data['status']) {
                            'in_progress' => $record->markAsInProgress(),
                            'resolved' => $record->markAsResolved(),
                            'closed' => $record->markAsClosed(),
                            default => $record->update(['status' => 'new']),
                        };
                    }),

                Action::make('set_priority')
                    ->label(__('Change Priority'))
                    ->icon('heroicon-o-flag')
                    ->modalHeading(__('Change Priority'))
                    ->form([
                        Select::make('priority')
                            ->label(__('Priority'))
                            ->options([
                                'low' => __('Low'),
                                'medium' => __('Medium'),
                                'high' => __('High'),
                                'urgent' => __('Urgent'),
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn (Model $record): array => [
                        'priority' => $record->priority,
                    ])
                    ->action(fn (Model $record, array $data) => $record->update(['priority' => $data['priority']])),

                Action::make('unlink_resource')
                    ->label(__('Unlink Resource'))
                    ->icon('heroicon-o-link-slash')
                    ->requiresConfirmation()
                    ->color('warning')
                    ->visible(fn (Model $record): bool => method_exists($record, 'hasResource') && $record->hasResource())
                    ->action(function (Model $record): void {
                        /** @var ContactSubmission $record */
                        $extras = $record->extras ?? [];
                        unset($extras['resource_type'], $extras['resource_id']);
                        $record->update(['extras' => $extras]);
                    }),
            ])->label(__('Quick Actions'))->icon('heroicon-o-bolt'),
            Actions\DeleteAction::make(),
        ];
    }
}
