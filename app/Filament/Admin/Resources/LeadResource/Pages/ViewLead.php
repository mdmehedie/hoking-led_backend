<?php

namespace App\Filament\Admin\Resources\LeadResource\Pages;

use App\Filament\Admin\Resources\LeadResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    public function getView(): string
    {
        return 'filament.admin.resources.lead-resource.pages.view-lead';
    }
}
