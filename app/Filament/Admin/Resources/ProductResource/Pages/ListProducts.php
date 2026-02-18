<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import_products')
                ->label('Import Products')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import Products')
                ->modalDescription('Upload a CSV or Excel file with product data. Required column: title. Optional: slug, short_description, detailed_description, status, published_at, technical_specs (JSON), tags (comma-separated), category (by name or slug)')
                ->form([
                    FileUpload::make('file')
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                ])
                ->modalSubmitActionLabel('Import Products')
                ->modalFooterActions([
                    Actions\Action::make('submit_import')
                        ->label('Import Products')
                        ->icon('heroicon-o-check')
                        ->action(function () {
                            $data = $this->form->getState();
                            $file = $data['file'];
                            $path = $file->store('imports');
                            $import = new ProductImport;
                            Excel::import($import, storage_path('app/' . $path));
                            $errors = $import->getErrors();
                            $this->dispatch('toastr', ['type' => 'success', 'title' => 'Import completed', 'message' => 'Products imported/updated: ' . $import->getImportedCount() . ', Errors: ' . count($errors)]);
                        }),
                    Actions\Action::make('download_sample')
                        ->label('Download Sample CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function () {
                            $headers = ['Title', 'Slug', 'Short Description', 'Detailed Description', 'Status', 'Published At', 'Technical Specs', 'Tags', 'Category', 'Category Slug'];
                            $sample = ['Sample Product', 'sample-product', 'This is a short description', 'This is a detailed description', 'draft', '2026-02-18 12:00:00', '{"key":"value"}', 'tag1,tag2', 'Sample Category', 'sample-category'];

                            $callback = function() use ($headers, $sample) {
                                $file = fopen('php://output', 'w');
                                fputcsv($file, $headers);
                                fputcsv($file, $sample);
                                fclose($file);
                            };

                            return response()->streamDownload($callback, 'product_sample.csv', ['Content-Type' => 'text/csv']);
                        })
                ]),
            Actions\Action::make('export_products')
                ->label('Export Products')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->form([
                    Select::make('format')
                        ->label('Export Format')
                        ->options(['csv' => 'CSV', 'xlsx' => 'Excel'])
                        ->required()
                        ->default('csv')
                ])
                ->action(function (array $data) {
                    $format = $data['format'];
                    $fileName = 'products.' . $format;
                    return Excel::download(new ProductExport, $fileName);
                }),
        ];
    }
}
