<?php

namespace App\Filament\Imports;

use App\Models\NewsletterSubscription;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class NewsletterSubscriberImporter extends Importer
{
    protected static ?string $model = NewsletterSubscription::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),

            ImportColumn::make('first_name')
                ->label('First Name')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('last_name')
                ->label('Last Name')
                ->rules(['nullable', 'max:255']),
        ];
    }

    public function resolveRecord(): ?NewsletterSubscription
    {
        return NewsletterSubscription::firstOrCreate(
            ['email' => strtolower($this->data['email'])],
            [
                'first_name' => $this->data['first_name'] ?? '',
                'last_name' => $this->data['last_name'] ?? '',
                'status' => 'active',
                'source' => 'import',
                'subscribed_at' => now(),
                'unsubscribe_token' => Str::uuid(),
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return $import->successful_rows . ' subscribers imported. ' . $import->failures()->count() . ' failed.';
    }
}
