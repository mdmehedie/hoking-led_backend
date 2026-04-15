<?php

namespace App\Filament\Admin\Resources\ConversationResource\Table;

use App\Filament\Admin\Resources\ConversationResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ConversationTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('last_visitor_message_at', 'desc')
            ->columns([
                TextColumn::make('visitor_name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('visitor_email')
                    ->label(__('Email'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('Email copied')),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('country')
                    ->label(__('Country'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('company_name')
                    ->label(__('Company'))
                    ->searchable()
                    ->toggleable(),
                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'info' => 'new',
                        'warning' => 'awaiting_admin',
                        'success' => 'awaiting_visitor',
                        'info' => 'resolved',
                        'gray' => 'closed',
                        'danger' => 'reopened',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'New',
                        'awaiting_admin' => 'Needs Response',
                        'awaiting_visitor' => 'Awaiting Visitor',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                        'reopened' => 'Reopened',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                TextColumn::make('priority')
                    ->label(__('Priority'))
                    ->badge()
                    ->colors([
                        'gray' => 'low',
                        'info' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                IconColumn::make('has_unread')
                    ->label('🔔')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record): bool => $record->hasUnreadFromVisitor())
                    ->toggleable(),
                TextColumn::make('last_sender')
                    ->label(__('Last From'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'visitor' ? 'warning' : 'success')
                    ->formatStateUsing(fn (string $state): string => $state === 'visitor' ? '👤 Visitor' : '👨‍💼 Admin')
                    ->toggleable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned To'))
                    ->placeholder('Unassigned')
                    ->toggleable(),
                TextColumn::make('last_visitor_message_at')
                    ->label(__('Last Visitor Message'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Started'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'new' => 'New',
                        'awaiting_admin' => 'Needs Response',
                        'awaiting_visitor' => 'Awaiting Visitor',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                        'reopened' => 'Reopened',
                    ]),
                Filter::make('needs_response')
                    ->label('🔔 Needs Response')
                    ->query(fn (Builder $query): Builder => $query->needsResponse()),
                Filter::make('reopened')
                    ->label('🔄 Reopened')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'reopened')),
                SelectFilter::make('priority')
                    ->label(__('Priority'))
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                SelectFilter::make('assigned_to')
                    ->label(__('Assigned To'))
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
                Filter::make('unassigned')
                    ->label('Unassigned Only')
                    ->query(fn (Builder $query): Builder => $query->whereNull('assigned_to')),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view')
                        ->label(__('View'))
                        ->url(fn ($record) => ConversationResource::getUrl('view', ['record' => $record]))
                        ->icon('heroicon-o-eye'),
                    Action::make('reply')
                        ->label(__('Reply'))
                        ->icon('heroicon-o-paper-airplane')
                        ->modalHeading(__('Reply to Conversation'))
                        ->form([
                            Textarea::make('message')
                                ->label('Message')
                                ->required()
                                ->rows(4),
                            Select::make('is_internal')
                                ->label('Visibility')
                                ->options([
                                    'false' => 'Visible to Visitor',
                                    'true' => 'Internal Note (Admin Only)',
                                ])
                                ->default('false')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $isInternal = $data['is_internal'] === 'true';
                            $record->adminReply($data['message'], auth()->id(), $isInternal);

                            Notification::make()
                                ->success()
                                ->title($isInternal ? 'Internal note added' : 'Reply sent')
                                ->send();
                        })
                        ->modalSubmitActionLabel('Send Reply'),
                    Action::make('assign')
                        ->label(__('Assign'))
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Select::make('assigned_to')
                                ->label('Assign to')
                                ->options(User::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(fn ($record, array $data) => $record->assignTo($data['assigned_to']))
                        ->modalSubmitActionLabel('Assign'),
                    Action::make('mark_resolved')
                        ->label(__('Mark Resolved'))
                        ->action(fn ($record) => $record->markAsResolved())
                        ->visible(fn ($record): bool => !in_array($record->status, ['resolved', 'closed']))
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    Action::make('mark_closed')
                        ->label(__('Mark Closed'))
                        ->action(fn ($record) => $record->markAsClosed())
                        ->visible(fn ($record): bool => $record->status !== 'closed')
                        ->color('gray')
                        ->icon('heroicon-o-archive-box'),
                    Action::make('delete')
                        ->label(__('Delete'))
                        ->action(fn ($record) => $record->delete())
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                ])->tooltip('Actions'),
            ])
            ->bulkActions([
                BulkAction::make('assign')
                    ->label(__('Assign To'))
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Select::make('assigned_to')
                            ->label('Assign to')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, Collection $records) {
                        $records->each->assignTo($data['assigned_to']);
                        Notification::make()
                            ->success()
                            ->title('Assigned')
                            ->body($records->count() . ' conversations assigned.')
                            ->send();
                    })
                    ->requiresConfirmation(),
                BulkAction::make('mark_resolved')
                    ->label(__('Mark Resolved'))
                    ->action(function (Collection $records) {
                        $records->each->markAsResolved();
                        Notification::make()
                            ->success()
                            ->title('Resolved')
                            ->body($records->count() . ' conversations resolved.')
                            ->send();
                    })
                    ->color('success'),
                BulkAction::make('mark_closed')
                    ->label(__('Mark Closed'))
                    ->action(function (Collection $records) {
                        $records->each->markAsClosed();
                        Notification::make()
                            ->success()
                            ->title('Closed')
                            ->body($records->count() . ' conversations closed.')
                            ->send();
                    })
                    ->color('gray'),
                BulkAction::make('delete')
                    ->label(__('Delete Selected'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->delete();
                        Notification::make()
                            ->success()
                            ->title('Deleted')
                            ->body($records->count() . ' conversations deleted.')
                            ->send();
                    }),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['assignedUser', 'lastMessage']))
            ->recordUrl(fn ($record) => ConversationResource::getUrl('view', ['record' => $record]));
    }
}
