<?php

namespace App\Filament\Admin\Resources\AppSettingResource\Table;

use Filament\Tables;
use Filament\Tables\Table;

class AppSettingTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('primary_color'),
            Tables\Columns\TextColumn::make('font_family'),
        ]);
    }
}
