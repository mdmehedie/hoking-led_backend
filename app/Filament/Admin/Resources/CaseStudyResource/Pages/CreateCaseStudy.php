<?php

namespace App\Filament\Admin\Resources\CaseStudyResource\Pages;

use App\Filament\Admin\Resources\CaseStudyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCaseStudy extends CreateRecord
{
    protected static string $resource = CaseStudyResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('create case study');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        $this->dispatch('toastr', [
            'type' => 'success',
            'title' => __('Case study created'),
            'message' => __('The case study has been created successfully.'),
        ]);
    }
}
