<?php

namespace App\Filament\Admin\Resources\NewsletterSubscriptionResource\Table;

use App\Filament\Exports\NewsletterSubscriberExporter;
use App\Models\NewsletterSubscription;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use League\Csv\Reader as CsvReader;

class NewsletterSubscriptionTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export')
                    ->label(__('Export Subscribers'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading(__('Export Newsletter Subscribers'))
                    ->modalDescription('Select conditions and format for export.')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'all' => 'All',
                                'active' => 'Active (Subscribed)',
                                'unsubscribed' => 'Unsubscribed',
                                'pending' => 'Pending',
                                'bounced' => 'Bounced',
                            ])
                            ->default('all'),
                        \Filament\Forms\Components\Select::make('source')
                            ->label('Source')
                            ->options([
                                'all' => 'All',
                                'website' => 'Website',
                                'footer' => 'Footer',
                                'popup' => 'Popup',
                                'checkout' => 'Checkout',
                                'landing_page' => 'Landing Page',
                                'import' => 'Import',
                            ])
                            ->default('all'),
                        \Filament\Forms\Components\Select::make('format')
                            ->label('Format')
                            ->options([
                                'csv' => 'CSV',
                                'xlsx' => 'Excel (.xlsx)',
                            ])
                            ->default('csv')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $query = NewsletterSubscription::query();

                        if ($data['status'] !== 'all') {
                            $query->where('status', $data['status']);
                        }

                        if ($data['source'] !== 'all') {
                            $query->where('source', $data['source']);
                        }

                        $columns = ['email', 'first_name', 'last_name', 'status', 'source', 'subscribed_at', 'unsubscribed_at', 'created_at'];
                        $rows = $query->select($columns)->get();

                        $count = $rows->count();

                        if ($count === 0) {
                            Notification::make()
                                ->warning()
                                ->title('No records found')
                                ->body('No subscribers match your selected conditions.')
                                ->send();
                            return;
                        }

                        if ($data['format'] === 'xlsx') {
                            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();

                            $sheet->setCellValue('A1', 'email');
                            $sheet->setCellValue('B1', 'first_name');
                            $sheet->setCellValue('C1', 'last_name');

                            $rowNum = 2;
                            foreach ($rows as $row) {
                                $sheet->setCellValue('A' . $rowNum, $row->email);
                                $sheet->setCellValue('B' . $rowNum, $row->first_name ?? '');
                                $sheet->setCellValue('C' . $rowNum, $row->last_name ?? '');
                                $rowNum++;
                            }

                            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                            $fileName = 'subscribers_' . now()->format('Y-m-d_His') . '.xlsx';
                            $tempPath = storage_path('app/temp/' . $fileName);

                            if (!file_exists(dirname($tempPath))) {
                                mkdir(dirname($tempPath), 0755, true);
                            }

                            $writer->save($tempPath);

                            return response()->download($tempPath)->deleteFileAfterSend(true);
                        }

                        $output = fopen('php://temp', 'w');
                        fputcsv($output, ['email', 'first_name', 'last_name']);

                        foreach ($rows as $row) {
                            fputcsv($output, [
                                $row->email,
                                $row->first_name ?? '',
                                $row->last_name ?? '',
                            ]);
                        }

                        rewind($output);
                        $csvContent = stream_get_contents($output);
                        fclose($output);

                        $fileName = 'subscribers_' . now()->format('Y-m-d_His') . '.csv';
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
                    ->label(__('Import Subscribers'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading(__('Import Newsletter Subscribers'))
                    ->modalDescription(new \Illuminate\Support\HtmlString(
                        'Upload a CSV or Excel file with columns: <strong>email</strong>, <strong>first_name</strong> (optional), <strong>last_name</strong> (optional).' . ' <a href="' . url('templates/newsletter-subscribers.csv') . '" style="color: #059669;" download>' . __('Download CSV') . '</a> | <a href="' . url('templates/newsletter-subscribers.xlsx') . '" style="color: #2563eb;" download>' . __('Download Excel') . '</a>'
                    ))
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label(__('File'))
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

                        if (!$file instanceof \Illuminate\Http\UploadedFile) {
                            Notification::make()
                                ->danger()
                                ->title('No file uploaded')
                                ->send();
                            return;
                        }

                        $extension = $file->getClientOriginalExtension();
                        $rows = [];

                        if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
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
                                $email = trim($row['A'] ?? '');
                                $firstName = trim($row['B'] ?? '');
                                $lastName = trim($row['C'] ?? '');
                            } else {
                                $email = trim($row['email'] ?? $row['Email'] ?? $row['EMAIL'] ?? '');
                                $firstName = trim($row['first_name'] ?? $row['First Name'] ?? $row['first_name'] ?? '');
                                $lastName = trim($row['last_name'] ?? $row['Last Name'] ?? $row['last_name'] ?? '');
                            }

                            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $skipped++;
                                continue;
                            }

                            $existing = NewsletterSubscription::where('email', strtolower($email))->first();

                            if ($existing) {
                                $skipped++;
                                continue;
                            }

                            NewsletterSubscription::create([
                                'email' => strtolower($email),
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'status' => 'active',
                                'source' => 'import',
                                'subscribed_at' => now(),
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
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('Email address copied'))
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label(__('First Name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_name')
                    ->label(__('Last Name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'gray' => 'unsubscribed',
                        'danger' => 'bounced',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('source')
                    ->label(__('Source'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subscribed_at')
                    ->label(__('Subscribed'))
                    ->dateTime('M j, Y')
                    ->sortable(),
                TextColumn::make('unsubscribed_at')
                    ->label(__('Unsubscribed'))
                    ->dateTime('M j, Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'active' => __('Active'),
                        'unsubscribed' => __('Unsubscribed'),
                        'bounced' => __('Bounced'),
                    ]),
                SelectFilter::make('source')
                    ->label(__('Source'))
                    ->options([
                        'website' => __('Website'),
                        'footer' => __('Footer'),
                        'popup' => __('Popup'),
                        'checkout' => __('Checkout'),
                        'landing_page' => __('Landing Page'),
                        'import' => __('Import'),
                    ]),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('View'))
                    ->url(fn ($record) => \App\Filament\Admin\Resources\NewsletterSubscriptionResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                Action::make('activate')
                    ->label(__('Activate'))
                    ->action(fn ($record) => $record->markAsActive())
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status !== 'active')
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Action::make('unsubscribe')
                    ->label(__('Unsubscribe'))
                    ->action(fn ($record) => $record->unsubscribe())
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status !== 'unsubscribed')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
                Action::make('delete')
                    ->label(__('Delete'))
                    ->action(fn ($record) => $record->delete())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                BulkAction::make('activate')
                    ->label(__('Activate Selected'))
                    ->action(function (Collection $records) {
                        $count = 0;
                        $records->each(function ($record) use (&$count) {
                            if ($record->status !== 'active') {
                                $record->markAsActive();
                                $count++;
                            }
                        });
                        Notification::make()
                            ->success()
                            ->title(__('Activated'))
                            ->body($count . ' ' . __('subscriptions activated.'))
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
                BulkAction::make('unsubscribe')
                    ->label(__('Unsubscribe Selected'))
                    ->action(function (Collection $records) {
                        $count = 0;
                        $records->each(function ($record) use (&$count) {
                            if ($record->status !== 'unsubscribed') {
                                $record->unsubscribe();
                                $count++;
                            }
                        });
                        Notification::make()
                            ->success()
                            ->title(__('Unsubscribed'))
                            ->body($count . ' ' . __('subscriptions unsubscribed.'))
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
                BulkAction::make('delete')
                    ->label(__('Delete Selected'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()
                            ->success()
                            ->title(__('Deleted'))
                            ->body($count . ' ' . __('items deleted successfully.'))
                            ->send();
                    }),
            ]);
    }
}
