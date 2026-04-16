<?php

namespace App\Filament\Admin\Resources\ConversationResource\Pages;

use App\Filament\Admin\Resources\ConversationResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

//    protected function getHeaderActions(): array
//    {
//        return [
//            Action::make('reply')
//                ->label('Reply')
//                ->icon('heroicon-o-paper-airplane')
//                ->modalHeading('Reply to Conversation')
//                ->form([
//                    Textarea::make('message')
//                        ->label('Message')
//                        ->required()
//                        ->rows(4),
//                    Select::make('is_internal')
//                        ->label('Visibility')
//                        ->options([
//                            'false' => 'Visible to Visitor',
//                            'true' => 'Internal Note (Admin Only)',
//                        ])
//                        ->default('false')
//                        ->required(),
//                ])
//                ->action(function ($record, array $data) {
//                    $isInternal = $data['is_internal'] === 'true';
//                    $record->adminReply($data['message'], auth()->id(), $isInternal);
//
//                    Notification::make()
//                        ->success()
//                        ->title($isInternal ? 'Internal note added' : 'Reply sent')
//                        ->send();
//                })
//                ->modalSubmitActionLabel('Send Reply'),
//            Action::make('mark_resolved')
//                ->label('Mark Resolved')
//                ->action(function ($record) {
//                    $record->markAsResolved();
//                    $this->refreshFormData();
//                    Notification::make()
//                        ->success()
//                        ->title('Conversation marked as resolved.')
//                        ->send();
//                })
//                ->visible(fn ($record): bool => !in_array($record->status, ['resolved', 'closed']))
//                ->color('success')
//                ->icon('heroicon-o-check-circle'),
//            Action::make('mark_closed')
//                ->label('Mark Closed')
//                ->action(function ($record) {
//                    $record->markAsClosed();
//                    $this->refreshFormData();
//                    Notification::make()
//                        ->success()
//                        ->title('Conversation marked as closed.')
//                        ->send();
//                })
//                ->visible(fn ($record): bool => $record->status !== 'closed')
//                ->color('gray')
//                ->icon('heroicon-o-archive-box'),
//        ];
//    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Conversation Details')
                    ->schema([
                        TextEntry::make('visitor_name')->label('Name'),
                        TextEntry::make('visitor_email')->label('Email'),
                        TextEntry::make('phone')->label('Phone')->placeholder('Not provided'),
                        TextEntry::make('country')->label('Country')->placeholder('Not provided'),
                        TextEntry::make('company_name')->label('Company')->placeholder('Not provided'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'new' => 'info',
                                'awaiting_admin' => 'warning',
                                'awaiting_visitor' => 'success',
                                'resolved' => 'info',
                                'closed' => 'gray',
                                'reopened' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'new' => 'New',
                                'awaiting_admin' => 'Needs Response',
                                'awaiting_visitor' => 'Awaiting Visitor',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                                'reopened' => 'Reopened',
                                default => ucfirst($state),
                            }),
                        TextEntry::make('priority')
                            ->label('Priority')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'low' => 'gray',
                                'medium' => 'info',
                                'high' => 'warning',
                                'urgent' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('assignedUser.name')->label('Assigned To')->placeholder('Unassigned'),
                        TextEntry::make('session_id')->label('Session ID')->fontFamily('mono'),
                        TextEntry::make('created_at')->label('Started')->dateTime('M j, Y g:i A'),
                    ])->columns(2),
                Section::make('Conversation Messages')
                    ->headerActions([
                        Action::make('reply')
                            ->label('Reply')
                            ->icon('heroicon-o-paper-airplane')
                            ->modalHeading('Reply to Conversation')
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
                            }),

//                        Action::make('mark_resolved')
//                            ->label('Resolve')
//                            ->action(fn ($record) => $record->markAsResolved())
//                            ->color('success'),
//
//                        Action::make('mark_closed')
//                            ->label('Close')
//                            ->action(fn ($record) => $record->markAsClosed())
//                            ->color('gray'),
                    ])
                    ->schema([
                        RepeatableEntry::make('messages')
                            ->schema([
                                Section::make()
                                    ->heading(fn ($record) => match (true) {
                                        $record->is_internal => "🔒 Internal • {$record->created_at->diffForHumans()}",
                                        $record->is_admin => "Admin • {$record->created_at->diffForHumans()}",
                                        default => "{$record->sender_name} • {$record->created_at->diffForHumans()}",
                                    })
                                    ->schema([
                                        TextEntry::make('message')
                                            ->hiddenLabel()
                                            ->markdown()
                                            ->columnSpanFull()
                                            ->extraAttributes([
                                                'class' => 'prose max-w-none text-sm leading-relaxed'
                                            ]),
                                    ])
                                    ->extraAttributes(fn ($record) => [
                                        'class' => match (true) {
                                            $record->is_internal => 'border-l-4 border-yellow-500 bg-yellow-50 p-4 rounded-xl max-w-2xl',
                                            $record->is_admin => 'ml-auto bg-primary-50 border border-primary-200 p-4 rounded-xl max-w-2xl',
                                            default => 'mr-auto bg-gray-50 border border-gray-200 p-4 rounded-xl max-w-2xl',
                                        }
                                    ])
                            ])
                            ->contained(false)
                            ->extraAttributes([
                                'class' => 'space-y-4',
                                'wire:poll.5s' => ''
                            ])
                    ])
            ]);
    }

    protected function getFooterActions(): array
    {
        return [
            Action::make('assign')
                ->label('Assign To')
                ->form([
                    Select::make('assigned_to')
                        ->label('Assign to')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(fn ($record, array $data) => $record->assignTo($data['assigned_to']))
                ->modalSubmitActionLabel('Assign'),
        ];
    }
}
