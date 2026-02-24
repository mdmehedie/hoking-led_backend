<?php

namespace App\Filament\Admin\Pages;

use App\Models\Form as FormModel;
use App\Models\FormWebhook;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form as FilamentForm;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WebhookManager extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.admin.pages.webhook-manager';

    protected static ?string $title = 'Webhook Manager';

    protected static ?string $slug = 'webhook-manager';

    public function mount(): void
    {
        // This page redirects to forms for webhook management
        redirect()->route('filament.admin.resources.forms.index');
    }
}
