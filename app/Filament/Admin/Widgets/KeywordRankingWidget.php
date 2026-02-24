<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;

class KeywordRankingWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        // In a real implementation, integrate with SEMrush, Ahrefs, Moz, etc.
        // For now, show no data available

        return $table
            ->records(fn () => [['message' => 'No SEO data available. Connect to an SEO service for keyword tracking.']])
            ->columns([
                Tables\Columns\TextColumn::make('message')
                    ->label('')
                    ->state('No SEO data available. Connect to an SEO service for keyword tracking.'),
            ])
            ->heading('Keyword Rankings')
            ->description('Connect to SEO service for keyword tracking');
    }
}
