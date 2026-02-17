<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Actions\Action;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings extends Page
{
    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Logos')->schema([
                FileUpload::make('logo_light')->image()->directory('settings')->acceptedFileTypes(['image/*']),
                FileUpload::make('logo_dark')->image()->directory('settings')->acceptedFileTypes(['image/*']),
            ]),
            Section::make('Favicon')->schema([
                FileUpload::make('favicon')->image()->directory('settings')->acceptedFileTypes(['image/*']),
            ]),
            Section::make('Brand Colors')->schema([
                ColorPicker::make('primary_color'),
                ColorPicker::make('secondary_color'),
                ColorPicker::make('accent_color'),
            ]),
            Section::make('Typography')->schema([
                Select::make('font_family')->options([
                    'Arial' => 'Arial',
                    'Helvetica' => 'Helvetica',
                    'Times New Roman' => 'Times New Roman',
                    'Courier New' => 'Courier New',
                ]),
                TextInput::make('base_font_size'),
            ]),
        ]);
    }

    public function mount(): void
    {
        $data = [];
        $keys = ['logo_light', 'logo_dark', 'favicon', 'primary_color', 'secondary_color', 'accent_color', 'font_family', 'base_font_size'];
        foreach ($keys as $key) {
            $data[$key] = setting($key);
        }
        $this->form->fill($data);
    }

    protected function getActions(): array
    {
        return [
            Action::make('save')
                ->action(function () {
                    $data = $this->form->getState();
                    $keys = ['logo_light', 'logo_dark', 'favicon', 'primary_color', 'secondary_color', 'accent_color', 'font_family', 'base_font_size'];
                    foreach ($keys as $key) {
                        Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? null]);
                        Cache::forget('setting.' . $key);
                    }
                    $this->notify('success', 'Settings updated successfully.');
                }),
        ];
    }
}
