<?php

namespace App\Filament\Admin\Pages;

use App\Models\AppSetting;
use App\Filament\Forms\Components\TinyEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Utilities\Get;

class EmailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected string $view = 'filament.admin.pages.email-settings';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->can('viewAny', AppSetting::class);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $settings = AppSetting::first();
        
        if ($settings) {
            $this->form->fill($settings->toArray());
        }
    }

    public static function getNavigationLabel(): string
    {
        return __('Email Notifications');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public function getTitle(): string
    {
        return __('Email Notification Settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('Internal Alerts (Staff)'))
                    ->description(__('Configure how your team receives new lead notifications.'))
                    ->schema([
                        Toggle::make('contact_internal_enabled')
                            ->label(__('Enable Internal Alerts'))
                            ->default(true)
                            ->live(),
                        TagsInput::make('contact_internal_recipients')
                            ->label(__('Recipient Emails'))
                            ->placeholder(__('Add email and press enter'))
                            ->helperText(__('Specific email addresses that should receive alerts.'))
                            ->visible(fn (Get $get) => $get('contact_internal_enabled')),
                        
                        TextInput::make("contact_internal_subject")
                            ->label(__('Subject Line'))
                            ->placeholder(__('New Contact Submission: {{subject}}'))
                            ->visible(fn (Get $get) => $get('contact_internal_enabled')),
                        TinyEditor::make("contact_internal_template")
                            ->label(__('Email Template'))
                            ->helperText(__('Available tags: {{name}}, {{email}}, {{phone}}, {{company}}, {{country}}, {{subject}}, {{message}}'))
                            ->visible(fn (Get $get) => $get('contact_internal_enabled')),
                    ]),

                Section::make(__('External Acknowledgement (Visitor)'))
                    ->description(__('If enabled, an automated confirmation message will be sent to the visitor.'))
                    ->schema([
                        Toggle::make('contact_external_enabled')
                            ->label(__('Enable Auto-Reply'))
                            ->default(true)
                            ->live(),
                        
                        TextInput::make("contact_external_subject")
                            ->label(__('Subject Line'))
                            ->placeholder(__('Thank you for contacting us, {{name}}'))
                            ->visible(fn (Get $get) => $get('contact_external_enabled')),
                        TinyEditor::make("contact_external_template")
                            ->label(__('Email Template'))
                            ->helperText(__('Available tags: {{name}}, {{subject}}'))
                            ->visible(fn (Get $get) => $get('contact_external_enabled')),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save Settings'))
                ->visible(fn (): bool => auth()->user()->can('update', AppSetting::first() ?? new AppSetting()))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $settings = AppSetting::first();

        if ($settings) {
            abort_unless(auth()->user()->can('update', $settings), 403);
        } else {
            abort_unless(auth()->user()->can('create', AppSetting::class), 403);
        }
        
        if ($settings) {
            $settings->update($this->form->getState());
        } else {
            AppSetting::create($this->form->getState());
            $this->form->fill(AppSetting::first()?->toArray() ?? []);
            
        }

        Notification::make()
            ->title(__('Settings saved successfully!'))
            ->success()
            ->send();
    }
}
