<?php

namespace App\Filament\Admin\Resources\CaseStudyCategoryResource\Pages;

use App\Filament\Admin\Resources\CaseStudyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaseStudyCategory extends EditRecord
{
    protected static string $resource = CaseStudyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
