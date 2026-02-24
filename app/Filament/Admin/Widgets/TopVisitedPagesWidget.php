<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\GA4Service;

class TopVisitedPagesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        try {
            $ga4Service = new GA4Service();
            $topPages = $ga4Service->getTopVisitedPages(10);

            $data = collect($topPages)->map(function ($page, $index) {
                return [
                    'rank' => $index + 1,
                    'page_path' => $page['page_path'],
                    'page_views' => $page['page_views'],
                ];
            })->toArray();

            return $table
                ->records(fn () => $data)
                ->columns([
                    Tables\Columns\TextColumn::make('rank')
                        ->label('#')
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('page_path')
                        ->label('Page Path')
                        ->sortable(false)
                        ->limit(50),
                    Tables\Columns\TextColumn::make('page_views')
                        ->label('Page Views')
                        ->sortable(false)
                        ->alignEnd(),
                ])
                ->heading('Top Visited Pages (Last 30 Days)')
                ->description('Most visited pages from Google Analytics 4')
                ->defaultSort('page_views', 'desc');
        } catch (\Exception $e) {
            return $table
                ->records(fn () => [['message' => 'GA4 not configured. Please set up GA4 credentials in Settings.']])
                ->columns([
                    Tables\Columns\TextColumn::make('message')
                        ->label('')
                        ->state('GA4 not configured. Please set up GA4 credentials in Settings.'),
                ])
                ->heading('Top Visited Pages')
                ->description('Configure GA4 to see page analytics');
        }
    }
}
