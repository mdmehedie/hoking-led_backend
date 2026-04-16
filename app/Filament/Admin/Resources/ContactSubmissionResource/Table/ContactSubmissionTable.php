<?php

namespace App\Filament\Admin\Resources\ContactSubmissionResource\Table;

use App\Filament\Admin\Resources\ContactSubmissionResource;
use App\Models\ContactSubmission;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Http\UploadedFile;
use League\Csv\Reader as CsvReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ContactSubmissionTable
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
                        $query = ContactSubmission::query();

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

                        $columns = ['name', 'email', 'phone', 'country', 'subject', 'message', 'status', 'priority', 'source', 'created_at'];

                        if ($data['format'] === 'xlsx') {
                            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();

                            $sheet->setCellValue('A1', 'name');
                            $sheet->setCellValue('B1', 'email');
                            $sheet->setCellValue('C1', 'phone');
                            $sheet->setCellValue('D1', 'country');
                            $sheet->setCellValue('E1', 'subject');
                            $sheet->setCellValue('F1', 'message');
                            $sheet->setCellValue('G1', 'status');
                            $sheet->setCellValue('H1', 'priority');
                            $sheet->setCellValue('I1', 'source');
                            $sheet->setCellValue('J1', 'created_at');

                            $rowNum = 2;
                            foreach ($rows as $row) {
                                $sheet->setCellValue('A' . $rowNum, $row->name ?? '');
                                $sheet->setCellValue('B' . $rowNum, $row->email ?? '');
                                $sheet->setCellValue('C' . $rowNum, $row->phone ?? '');
                                $sheet->setCellValue('D' . $rowNum, $row->place ?? '');
                                $sheet->setCellValue('E' . $rowNum, $row->subject ?? '');
                                $sheet->setCellValue('F' . $rowNum, $row->message ?? '');
                                $sheet->setCellValue('G' . $rowNum, $row->status ?? '');
                                $sheet->setCellValue('H' . $rowNum, $row->priority ?? '');
                                $sheet->setCellValue('I' . $rowNum, $row->source ?? '');
                                $sheet->setCellValue('J' . $rowNum, $row->created_at?->format('Y-m-d H:i:s') ?? '');
                                $rowNum++;
                            }

                            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
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
                                $row->name ?? '',
                                $row->email ?? '',
                                $row->phone ?? '',
                                $row->place ?? '',
                                $row->subject ?? '',
                                $row->message ?? '',
                                $row->status ?? '',
                                $row->priority ?? '',
                                $row->source ?? '',
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
                Action::make('import')
                    ->label(__('Import'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading(__('Import Contact Submissions'))
                    ->modalDescription(new \Illuminate\Support\HtmlString(
                        'Upload a CSV or Excel file with columns: <strong>name</strong>, <strong>email</strong>, <strong>phone</strong> (optional), <strong>Country</strong> (optional), <strong>subject</strong>, <strong>message</strong>.<br>' .
                        '<a href="' . url('templates/contact-submissions.csv') . '" style="color: #059669;" download>Download CSV</a> | <a href="' . url('templates/contact-submissions.xlsx') . '" style="color: #2563eb;" download>Download Excel</a>'
                    ))
                    ->form([
                        FileUpload::make('file')
                            ->label('File')
                            ->acceptedFileTypes([
                                'text/csv',
                                'application/csv',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->storeFiles(false)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $file = $data['file'];

                        if (!$file instanceof UploadedFile) {
                            Notification::make()
                                ->danger()
                                ->title('No file uploaded')
                                ->send();
                            return;
                        }

                        $extension = $file->getClientOriginalExtension();
                        $rows = [];

                        if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                            $reader = new Xlsx();
                            $spreadsheet = $reader->load($file->getRealPath());
                            $sheet = $spreadsheet->getActiveSheet();
                            $rows = $sheet->toArray(null, true, true, true);
                            array_shift($rows);
                        } else {
                            $csv = CsvReader::createFromPath($file->getRealPath(), 'r');
                            $csv->setHeaderOffset(0);
                            $rows = iterator_to_array($csv);
                        }

                        $imported = 0;
                        $skipped = 0;

                        foreach ($rows as $row) {
                            if (is_array($row) && isset($row['A'])) {
                                $name = trim($row['A'] ?? '');
                                $email = trim($row['B'] ?? '');
                                $phone = trim($row['C'] ?? '');
                                $place = trim($row['D'] ?? '');
                                $subject = trim($row['E'] ?? '');
                                $message = trim($row['F'] ?? '');
                            } else {
                                $name = trim($row['name'] ?? $row['Name'] ?? '');
                                $email = trim($row['email'] ?? $row['Email'] ?? '');
                                $phone = trim($row['phone'] ?? $row['Phone'] ?? '');
                                $place = trim($row['place'] ?? $row['Place'] ?? '');
                                $subject = trim($row['subject'] ?? $row['Subject'] ?? '');
                                $message = trim($row['message'] ?? $row['Message'] ?? '');
                            }

                            if (empty($name) || empty($email)) {
                                $skipped++;
                                continue;
                            }

                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $skipped++;
                                continue;
                            }

                            ContactSubmission::create([
                                'name' => $name,
                                'email' => $email,
                                'phone' => $phone,
                                'place' => $place,
                                'subject' => $subject,
                                'message' => $message,
                                'status' => 'new',
                                'priority' => 'medium',
                                'source' => 'import',
                            ]);

                            $imported++;
                        }

                        Notification::make()
                            ->success()
                            ->title('Import Complete')
                            ->body('Imported ' . $imported . ', skipped ' . $skipped . '.')
                            ->send();
                    })
                    ->modalSubmitActionLabel(__('Import'))
                    ->modalWidth('2xl'),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('place')
                    ->label(__('Country'))
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
                SelectColumn::make('status')
                    ->label(__('Status'))
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ])
                    ->sortable(),
                SelectColumn::make('priority')
                    ->label(__('Priority'))
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
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
                    ->getStateUsing(function ($record) {
                        $type = $record->extras['resource_type'] ?? null;

                        if (!$type) {
                            return null;
                        }

                        return 'View ' . ucfirst(str_replace('_', ' ', $type));
                    })
                    ->url(function ($record) {
                        $type = $record->extras['resource_type'] ?? null;
                        $id = $record->extras['resource_id'] ?? null;

                        if (!$type || !$id) {
                            return null;
                        }

                        $resourceClass = ContactSubmission::RESOURCES[$type] ?? null;

                        if (!$resourceClass) {
                            return null;
                        }

                        return $resourceClass::getUrl('edit', ['record' => $id]);
                    })
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->icon('heroicon-o-arrow-top-right-on-square')
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
                ActionGroup::make([
                    Action::make('edit')
                        ->label(__('Edit'))
                        ->icon('heroicon-o-pencil')
                        ->url(fn ($record) => ContactSubmissionResource::getUrl('edit', ['record' => $record])),
                    Action::make('view')
                        ->label(__('View'))
                        ->url(fn ($record) => ContactSubmissionResource::getUrl('view', ['record' => $record]))
                        ->icon('heroicon-o-eye'),
                    Action::make('assign')
                        ->label(__('Assign'))
                        ->icon('heroicon-o-user-plus')
                        ->modalHeading(__('Assign Submission'))
                        ->form([
                            Select::make('assigned_to')
                                ->label('Assign to')
                                ->options(User::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(fn ($record, array $data) => $record->update(['assigned_to' => $data['assigned_to']]))
                        ->modalSubmitActionLabel('Assign'),
                    Action::make('link_resource')
                        ->label(__('Link Resource'))
                        ->icon('heroicon-o-link')
                        ->modalHeading(__('Link to Resource'))
                        ->modalDescription('Link this submission to a specific record for context.')
                        ->form([
                            Select::make('resource_type')
                                ->label('Resource Type')
                                ->options([
                                    'product' => 'Product',
                                    'blog' => 'Blog',
                                    'news' => 'News',
                                    'project' => 'Project',
                                    'page' => 'Page',
                                    'case_study' => 'Case Study',
                                    'brand' => 'Brand',
                                    'category' => 'Category',
                                ])
                                ->required()
                                ->live(),
                            Select::make('resource_id')
                                ->label('Resource')
                                ->options(function (callable $get) {
                                    $type = $get('resource_type');
                                    if (!$type) return [];

                                    $model = ContactSubmission::RESOURCE_TYPES[$type] ?? null;
                                    if (!$model) return [];

                                    return $model::limit(100)->pluck('title', 'id')->filter();
                                })
                                ->searchable()
                                ->required(),
                        ])
                        ->fillForm(fn ($record) => [
                            'resource_type' => $record->resource_type,
                            'resource_id' => $record->resource_id,
                        ])
                        ->action(function ($record, array $data) {
                            $extras = $record->extras ?? [];
                            $extras['resource_type'] = $data['resource_type'];
                            $extras['resource_id'] = $data['resource_id'];
                            $record->update(['extras' => $extras]);
                        })
                        ->modalSubmitActionLabel('Link'),
                    Action::make('unlink_resource')
                        ->label(__('Unlink'))
                        ->icon('heroicon-o-link-slash')
                        ->action(function ($record) {
                            $extras = $record->extras ?? [];
                            unset($extras['resource_type'], $extras['resource_id']);
                            $record->update(['extras' => $extras]);
                        })
                        ->visible(fn ($record): bool => $record->hasResource())
                        ->requiresConfirmation()
                        ->color('warning'),
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
                ])->tooltip('Actions'),
            ])
            ->bulkActions([
                BulkAction::make('set_status')
                    ->label(__('Set Status'))
                    ->icon('heroicon-o-flag')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, Collection $records) {
                        $records->each->update(['status' => $data['status']]);
                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body($records->count() . ' submissions updated.')
                            ->send();
                    })
                    ->requiresConfirmation(),
                BulkAction::make('set_priority')
                    ->label(__('Set Priority'))
                    ->icon('heroicon-o-exclamation-triangle')
                    ->form([
                        Select::make('priority')
                            ->label('Priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, Collection $records) {
                        $records->each->update(['priority' => $data['priority']]);
                        Notification::make()
                            ->success()
                            ->title('Priority Updated')
                            ->body($records->count() . ' submissions updated.')
                            ->send();
                    })
                    ->requiresConfirmation(),
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
                        $records->each->update(['assigned_to' => $data['assigned_to']]);
                        Notification::make()
                            ->success()
                            ->title('Assigned')
                            ->body($records->count() . ' submissions assigned.')
                            ->send();
                    })
                    ->requiresConfirmation(),
                BulkAction::make('unassign')
                    ->label(__('Unassign Selected'))
                    ->icon('heroicon-o-user-minus')
                    ->action(function (Collection $records) {
                        $records->each->update(['assigned_to' => null]);
                        Notification::make()
                            ->success()
                            ->title('Unassigned')
                            ->body($records->count() . ' submissions unassigned.')
                            ->send();
                    })
                    ->requiresConfirmation(),
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
