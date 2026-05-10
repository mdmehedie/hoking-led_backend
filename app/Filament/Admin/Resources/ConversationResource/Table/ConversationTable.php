<?php

namespace App\Filament\Admin\Resources\ConversationResource\Table;

use App\Filament\Admin\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConversationTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export')
                    ->label(__('Export'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('Export Contact Submissions'))
                    ->modalDescription('Select conditions and format for export.')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'all' => 'All',
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->default('all'),
                        Select::make('priority')
                            ->label('Priority')
                            ->options([
                                'all' => 'All',
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('all'),
                        Select::make('format')
                            ->label('Format')
                            ->options([
                                'csv' => 'CSV',
                                'xlsx' => 'Excel (.xlsx)',
                            ])
                            ->default('csv')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $query = Conversation::query();

                        if ($data['status'] !== 'all') {
                            $query->where('status', $data['status']);
                        }

                        if ($data['priority'] !== 'all') {
                            $query->where('priority', $data['priority']);
                        }

                        $rows = $query->get();
                        $count = $rows->count();

                        if ($count === 0) {
                            Notification::make()
                                ->warning()
                                ->title('No records found')
                                ->body('No submissions match your selected conditions.')
                                ->send();
                            return;
                        }

                        $columns = ['name', 'email', 'phone', 'country', 'status', 'priority', 'created_at'];

                        if ($data['format'] === 'xlsx') {
                            $spreadsheet = new Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();

                            $sheet->setCellValue('A1', 'name');
                            $sheet->setCellValue('B1', 'email');
                            $sheet->setCellValue('C1', 'phone');
                            $sheet->setCellValue('D1', 'country');
                            $sheet->setCellValue('E1', 'status');
                            $sheet->setCellValue('F1', 'priority');
                            $sheet->setCellValue('G1', 'created_at');

                            $rowNum = 2;
                            foreach ($rows as $row) {
                                $sheet->setCellValue('A' . $rowNum, $row->visitor_name ?? '');
                                $sheet->setCellValue('B' . $rowNum, $row->visitor_email ?? '');
                                $sheet->setCellValue('C' . $rowNum, $row->phone ?? '');
                                $sheet->setCellValue('D' . $rowNum, $row->country ?? '');
                                $sheet->setCellValue('E' . $rowNum, $row->status ?? '');
                                $sheet->setCellValue('F' . $rowNum, $row->priority ?? '');
                                $sheet->setCellValue('G' . $rowNum, $row->created_at?->format('Y-m-d H:i:s') ?? '');
                                $rowNum++;
                            }

                            $writer = new Xlsx($spreadsheet);
                            $fileName = 'contacts_' . now()->format('Y-m-d_His') . '.xlsx';
                            $tempPath = storage_path('app/temp/' . $fileName);

                            if (!file_exists(dirname($tempPath))) {
                                mkdir(dirname($tempPath), 0755, true);
                            }

                            $writer->save($tempPath);

                            return response()->download($tempPath)->deleteFileAfterSend(true);
                        }

                        $output = fopen('php://temp', 'w');
                        fputcsv($output, $columns);

                        foreach ($rows as $row) {
                            fputcsv($output, [
                                $row->visitor_name ?? '',
                                $row->visitor_email ?? '',
                                $row->phone ?? '',
                                $row->country ?? '',
                                $row->status ?? '',
                                $row->priority ?? '',
                                $row->created_at?->format('Y-m-d H:i:s') ?? '',
                            ]);
                        }

                        rewind($output);
                        $csvContent = stream_get_contents($output);
                        fclose($output);

                        $fileName = 'contacts_' . now()->format('Y-m-d_His') . '.csv';
                        $tempPath = storage_path('app/temp/' . $fileName);

                        if (!file_exists(dirname($tempPath))) {
                            mkdir(dirname($tempPath), 0755, true);
                        }

                        file_put_contents($tempPath, $csvContent);

                        return response()->download($tempPath)->deleteFileAfterSend(true);
                    })
                    ->modalSubmitActionLabel(__('Export'))
                    ->modalWidth('lg'),
            ])
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
                TextColumn::make('last_visitor_message_at')
                    ->label(__('Last Visitor Message'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('last_sender')
                    ->label(__('Last From'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'visitor' ? 'warning' : 'success')
                    ->formatStateUsing(fn (string $state): string => $state === 'visitor' ? '👤 Visitor' : '👨‍💼 Admin')
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
                SelectColumn::make('priority')
                    ->label(__('Priority'))
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->selectablePlaceholder(false),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned To'))
                    ->placeholder('Unassigned')
                    ->toggleable(),
                TextColumn::make('reply')
                    ->label(__('Reply'))
                    ->state('Reply')
                    ->extraAttributes(fn (Conversation $record): array => [
                        'class' => 'fi-color fi-color-primary fi-bg-color-400 hover:fi-bg-color-300 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-900 hover:fi-text-color-800 dark:fi-text-color-950 dark:hover:fi-text-color-950 fi-btn fi-size-md  fi-ac-btn-action',
                        // Prevent row click (recordUrl) from firing when clicking Reply.
                        'wire:click.stop' => "mountTableAction('reply', '{$record->getKey()}')",
                        'x-on:click.stop.prevent' => "\$wire.mountTableAction('reply', '{$record->getKey()}')",
                    ]),
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
                    self::replyAction(),
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
            ->recordUrl(fn ($record) => ConversationResource::getUrl('view', ['record' => $record]))
            ->extraAttributes(['wire:poll.3s' => '']);
    }

    private static function replyAction(): Action
    {
        return Action::make('reply')
            ->label(__('Reply'))
            ->icon('heroicon-o-paper-airplane')
            ->modalHeading(__('Reply to Conversation'))
            ->form([
                Placeholder::make('context_messages')
                    ->label('Recent conversation context')
                    ->content(function (Conversation $record): HtmlString {
                        $lastAdminAt = $record->messages()
                            ->where('sender_type', 'admin')
                            ->latest()
                            ->value('created_at');

                        $query = $record->messages()
                            ->visibleToVisitor()
                            ->when($lastAdminAt, fn ($q) => $q->where('created_at', '>=', $lastAdminAt))
                            ->orderBy('created_at', 'asc')
                            ->limit(20);

                        $lines = $query->get()->map(function ($m) {
                            $who = $m->sender_type === 'visitor' ? 'Visitor' : 'Admin';
                            $msg = trim((string) $m->message);
                            return [$who, $msg];
                        })->all();

                        // Render newest first in the UI.
                        $lines = array_reverse($lines);

                        if (!count($lines)) {
                            return new HtmlString('—');
                        }

                        $html = '<div style="display:flex;flex-direction:column;">';
                        foreach ($lines as [$who, $msg]) {
                            $html .= '<div style="display: flex;align-items: center; gap: 8px;padding:12px 12px 10px; ">';
                            $html .= '<span style="display:inline-flex;align-items:center;font-size:12px;font-weight:600;">' . e($who) . ' :</span>';
                            $html .= '<span style="white-space:pre-line;font-size:13px;line-height:1.45;">' . e($msg) . '</span>';
                            $html .= '</div>';
                        }
                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->columnSpanFull(),
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
            ->action(function (Conversation $record, array $data): void {
                if ($record->status === 'closed') {
                    Notification::make()
                        ->warning()
                        ->title('Conversation closed')
                        ->body('You cannot reply to a closed conversation.')
                        ->send();
                    return;
                }

                $isInternal = $data['is_internal'] === 'true';
                $record->adminReply($data['message'], auth()->id(), $isInternal);

                Notification::make()
                    ->success()
                    ->title($isInternal ? 'Internal note added' : 'Reply sent')
                    ->send();
            })
            ->extraModalFooterActions([
                Action::make('view_messages')
                    ->label('View Messages')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->outlined()
                    ->url(fn (Conversation $record) => ConversationResource::getUrl('view', ['record' => $record])),
            ])
            ->modalFooterActionsAlignment(Alignment::Left)
            ->modalSubmitAction(fn (Action $action, ?Conversation $record) => $action->disabled(
                (bool) ($record?->status === 'closed')
            ))
            ->modalSubmitActionLabel('Send Reply');
    }
}
