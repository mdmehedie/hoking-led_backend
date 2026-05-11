<?php

namespace App\Filament\Admin\Resources\NewsletterSubscriptionResource\Table;

use App\Filament\Admin\Resources\NewsletterSubscriptionResource;
use App\Models\NewsletterSubscription;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use League\Csv\Reader as CsvReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NewsletterSubscriptionTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export')
                    ->label(__('Export Subscribers'))
                    ->modalHeading(__('Export Newsletter Subscribers'))
                    ->visible(fn () => auth()->user()->can('viewAny', NewsletterSubscription::class))
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'all' => 'All',
                                'active' => 'Active (Subscribed)',
                                'unsubscribed' => 'Unsubscribed',
                                'pending' => 'Pending',
                                'bounced' => 'Bounced',
                            ])
                            ->default('all'),
                        Select::make('source')
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
                        abort_unless(auth()->user()->can('viewAny', NewsletterSubscription::class), 403);
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
                            $spreadsheet = new Spreadsheet();
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

                            $writer = new Xlsx($spreadsheet);
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
                    ->visible(fn () => auth()->user()->can('create', NewsletterSubscription::class))
                    ->modalDescription(new \Illuminate\Support\HtmlString(
                        'Upload a CSV or Excel file with columns: <strong>email</strong>, <strong>first_name</strong> (optional), <strong>last_name</strong> (optional).' . ' <a href="' . url('templates/newsletter-subscribers.csv') . '" style="color: #059669;" download>' . __('Download CSV') . '</a> | <a href="' . url('templates/newsletter-subscribers.xlsx') . '" style="color: #2563eb;" download>' . __('Download Excel') . '</a>'
                    ))
                    ->form([
                        FileUpload::make('file')
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
                        abort_unless(auth()->user()->can('create', NewsletterSubscription::class), 403);
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
                    ->url(fn ($record) => NewsletterSubscriptionResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                Action::make('activate')
                    ->label(__('Activate'))
                    ->action(function ($record): void {
                        abort_unless(auth()->user()->can('update', $record), 403);
                        $record->markAsActive();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status !== 'active' && auth()->user()->can('update', $record))
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Action::make('unsubscribe')
                    ->label(__('Unsubscribe'))
                    ->action(function ($record): void {
                        abort_unless(auth()->user()->can('update', $record), 403);
                        $record->unsubscribe();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status !== 'unsubscribed' && auth()->user()->can('update', $record))
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
                DeleteAction::make()
                    ->visible(fn ($record): bool => auth()->user()->can('delete', $record)),
            ])
            ->bulkActions([
                BulkAction::make('activate')
                    ->label(__('Activate Selected'))
                    ->action(function (Collection $records) {
                        abort_unless(auth()->user()->can('update', new NewsletterSubscription()), 403);
                        $count = 0;
                        $records->filter(fn ($record) => auth()->user()->can('update', $record))->each(function ($record) use (&$count) {
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
                    ->visible(fn () => auth()->user()->can('update', new NewsletterSubscription()))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
                BulkAction::make('unsubscribe')
                    ->label(__('Unsubscribe Selected'))
                    ->action(function (Collection $records) {
                        abort_unless(auth()->user()->can('update', new NewsletterSubscription()), 403);
                        $count = 0;
                        $records->filter(fn ($record) => auth()->user()->can('update', $record))->each(function ($record) use (&$count) {
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
                    ->visible(fn () => auth()->user()->can('update', new NewsletterSubscription()))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
                DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->can('delete', new NewsletterSubscription())),
            ]);
    }
}
