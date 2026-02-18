<?php

namespace App\Filament\Admin\Resources\FeaturedProductResource\Pages;

use App\Filament\Admin\Resources\FeaturedProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeaturedProducts extends ListRecords
{
    protected static string $resource = FeaturedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }
}
