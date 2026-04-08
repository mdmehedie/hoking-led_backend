<?php

namespace App\Filament\Admin\Resources\ContactSubmissionResource\Table;

use App\Filament\Admin\Resources\ContactSubmissionResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class ContactSubmissionTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('place')
                    ->label(__('Place'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('Email copied'))
                    ->toggleable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable()
                    ->limit(40)
                    ->sortable(),
                TextColumn::make('message')
                    ->label(__('Message'))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'success' => 'new',
                        'warning' => 'in_progress',
                        'info' => 'resolved',
                        'gray' => 'closed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                BadgeColumn::make('priority')
                    ->label(__('Priority'))
                    ->badge()
                    ->colors([
                        'gray' => 'low',
                        'info' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('source_label')
                    ->label(__('Source'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('source', 'like', "%{$search}%");
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('resource_label')
                    ->label(__('Related To'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned To'))
                    ->placeholder(__('Unassigned'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Received'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('responded_at')
                    ->label(__('Responded'))
                    ->dateTime('M j, Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('resolved_at')
                    ->label(__('Resolved'))
                    ->dateTime('M j, Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'new' => __('New'),
                        'in_progress' => __('In Progress'),
                        'resolved' => __('Resolved'),
                        'closed' => __('Closed'),
                    ]),
                SelectFilter::make('priority')
                    ->label(__('Priority'))
                    ->options([
                        'low' => __('Low'),
                        'medium' => __('Medium'),
                        'high' => __('High'),
                        'urgent' => __('Urgent'),
                    ]),
                SelectFilter::make('source')
                    ->label(__('Source'))
                    ->options([
                        'contact_page' => __('Contact Page'),
                        'footer' => __('Footer'),
                        'popup' => __('Popup'),
                        'support_page' => __('Support Page'),
                        'quote_request' => __('Quote Request'),
                        'api' => __('API'),
                    ]),
                SelectFilter::make('assigned_to')
                    ->label(__('Assigned To'))
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
                Filter::make('unassigned')
                    ->label(__('Unassigned Only'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('assigned_to')),
                Filter::make('has_resource')
                    ->label(__('Has Related Resource'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('extras->resource_type')),
                SelectFilter::make('resource_type')
                    ->label(__('Resource Type'))
                    ->options([
                        'product' => __('Product'),
                        'blog' => __('Blog'),
                        'news' => __('News'),
                        'project' => __('Project'),
                        'page' => __('Page'),
                        'case_study' => __('Case Study'),
                        'brand' => __('Brand'),
                        'category' => __('Category'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->where('extras->resource_type', $data['value']);
                    }),
                Filter::make('overdue')
                    ->label(__('Overdue (24h+)'))
                    ->query(fn (Builder $query): Builder => $query->awaitingSLA(24)),
                Filter::make('created_at')
                    ->label(__('Date Range'))
                    ->form([
                        DatePicker::make('created_from')->label(__('From')),
                        DatePicker::make('created_until')->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('View'))
                    ->url(fn ($record) => ContactSubmissionResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                Action::make('mark_in_progress')
                    ->label(__('Mark In Progress'))
                    ->action(fn ($record) => $record->markAsInProgress())
                    ->visible(fn ($record): bool => $record->status === 'new')
                    ->color('warning')
                    ->icon('heroicon-o-clock'),
                Action::make('mark_resolved')
                    ->label(__('Mark Resolved'))
                    ->action(fn ($record) => $record->markAsResolved())
                    ->visible(fn ($record): bool => $record->status !== 'resolved' && $record->status !== 'closed')
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
            ])
            ->bulkActions([
                BulkAction::make('mark_in_progress')
                    ->label(__('Mark In Progress'))
                    ->action(function (Collection $records) {
                        $count = $records->filter(fn ($r) => $r->status === 'new')->each->markAsInProgress()->count();
                        Notification::make()->success()->title(__('Updated'))->body($count . ' submissions updated.')->send();
                    })
                    ->color('warning')
                    ->icon('heroicon-o-clock'),
                BulkAction::make('mark_resolved')
                    ->label(__('Mark Resolved'))
                    ->action(function (Collection $records) {
                        $count = $records->filter(fn ($r) => !in_array($r->status, ['resolved', 'closed']))->each->markAsResolved()->count();
                        Notification::make()->success()->title(__('Updated'))->body($count . ' submissions resolved.')->send();
                    })
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
                BulkAction::make('mark_closed')
                    ->label(__('Mark Closed'))
                    ->action(function (Collection $records) {
                        $count = $records->filter(fn ($r) => $r->status !== 'closed')->each->markAsClosed()->count();
                        Notification::make()->success()->title(__('Updated'))->body($count . ' submissions closed.')->send();
                    })
                    ->color('gray')
                    ->icon('heroicon-o-archive-box'),
                BulkAction::make('delete')
                    ->label(__('Delete Selected'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()->success()->title(__('Deleted'))->body($count . ' items deleted.')->send();
                    }),
            ]);
    }
}
