<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\FormResource;
use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class RecentLeadsWidget extends BaseWidget
{
    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('form.name')
                    ->label('Form')
                    ->limit(20),
                Tables\Columns\TextColumn::make('data')
                    ->label('Contact Info')
                    ->formatStateUsing(function ($state) {
                        if (is_string($state)) {
                            $state = json_decode($state, true);
                        }
                        if (!is_array($state)) return '-';
                        $email = $state['email'] ?? null;
                        $phone = $state['phone'] ?? null;
                        return new HtmlString(
                            ($email ? e($email) : '') .
                            ($phone ? ($email ? '<br>' : '') . e($phone) : '')
                        );
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y g:i A'),
            ])
            ->heading('Recent Leads')
            ->description(new HtmlString(
                '<a href="' . FormResource::getUrl() . '" class="text-primary-600 hover:underline">View all forms →</a>'
            ))
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
