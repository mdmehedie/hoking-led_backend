<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\GA4Service;

class TrafficSourcesWidget extends BaseWidget
{
    protected static ?int $sort = 21;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 'full',
    ];

    public function table(Table $table): Table
    {
        try {
            $ga4Service = new GA4Service();
            $trafficSources = $ga4Service->getTrafficSources(10);

            $data = collect($trafficSources)->map(function ($source) {
                return [
                    'source' => $source['source'],
                    'sessions' => $source['sessions'],
                    'users' => $source['users'],
                ];
            })->toArray();

            return $table
                ->records(fn () => $data)
                ->columns([
                    Tables\Columns\TextColumn::make('source')
                        ->label('Traffic Source')
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('sessions')
                        ->label('Sessions')
                        ->sortable(false)
                        ->alignEnd(),
                    Tables\Columns\TextColumn::make('users')
                        ->label('Users')
                        ->sortable(false)
                        ->alignEnd(),
                ])
                ->heading('Traffic Sources (Last 30 Days)')
                ->description('Where your visitors are coming from')
                ->defaultSort('sessions', 'desc');
        } catch (\Exception $e) {
            return $table
                ->records(fn () => [['message' => 'GA4 not configured. Please set up GA4 credentials in Settings.']])
                ->columns([
                    Tables\Columns\TextColumn::make('message')
                        ->label('')
                        ->state('GA4 not configured. Please set up GA4 credentials in Settings.'),
                ])
                ->heading('Traffic Sources')
                ->description('Configure GA4 to see traffic analytics');
        }
    }
}
