<?php

namespace App\Filament\Exports;

use App\Models\NewsletterSubscription;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class NewsletterSubscriberExporter extends Exporter
{
    protected static ?string $model = NewsletterSubscription::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID')
                ->enabledByDefault(false),

            ExportColumn::make('email')
                ->label('Email')
                ->enabledByDefault(true),

            ExportColumn::make('first_name')
                ->label('First Name')
                ->enabledByDefault(true),

            ExportColumn::make('last_name')
                ->label('Last Name')
                ->enabledByDefault(true),

            ExportColumn::make('status')
                ->label('Status')
                ->enabledByDefault(true),

            ExportColumn::make('source')
                ->label('Source')
                ->enabledByDefault(false),

            ExportColumn::make('subscribed_at')
                ->label('Subscribed At')
                ->enabledByDefault(true),

            ExportColumn::make('unsubscribed_at')
                ->label('Unsubscribed At')
                ->enabledByDefault(false),

            ExportColumn::make('created_at')
                ->label('Created At')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = $export->successful_rows . ' subscriber rows exported.';

        if ($export->failed_rows_count > 0) {
            $body .= ' ' . $export->failed_rows_count . ' failed.';
        }

        return $body;
    }
}
