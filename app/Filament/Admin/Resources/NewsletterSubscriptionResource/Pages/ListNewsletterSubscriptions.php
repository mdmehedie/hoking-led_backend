<?php

namespace App\Filament\Admin\Resources\NewsletterSubscriptionResource\Pages;

use App\Filament\Admin\Resources\NewsletterSubscriptionResource;
use Filament\Resources\Pages\ListRecords;

class ListNewsletterSubscriptions extends ListRecords
{
    protected static string $resource = NewsletterSubscriptionResource::class;
}
