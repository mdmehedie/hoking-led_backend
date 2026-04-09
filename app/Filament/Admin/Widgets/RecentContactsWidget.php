<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class RecentContactsWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactSubmission::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(30),
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y g:i A'),
            ])
            ->heading('Recent Contact Submissions')
            ->description(new HtmlString(
                '<a href="' . ContactSubmissionResource::getUrl() . '" class="text-primary-600 hover:underline">View all →</a>'
            ))
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
