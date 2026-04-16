<?php

namespace App\Filament\Admin\Resources\ProductInquiryResource\Pages;

use App\Filament\Admin\Resources\ProductInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductInquiry extends ViewRecord
{
    protected static string $resource = ProductInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
