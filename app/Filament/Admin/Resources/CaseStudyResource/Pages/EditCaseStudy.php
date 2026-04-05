<?php

namespace App\Filament\Admin\Resources\CaseStudyResource\Pages;

use App\Filament\Admin\Resources\CaseStudyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaseStudy extends EditRecord
{
    protected static string $resource = CaseStudyResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('edit case study');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->dispatch('toastr', [
            'type' => 'success',
            'title' => __('Case study updated'),
            'message' => __('The case study has been updated successfully.'),
        ]);
    }
}
