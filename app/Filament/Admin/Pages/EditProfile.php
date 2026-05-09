<?php

namespace App\Filament\Admin\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Component;

class EditProfile extends BaseEditProfile
{
    /**
     * Disable the "simple" layout to show the sidebar and standard admin navigation.
     */
    public static function isSimple(): bool
    {
        return false;
    }

    /**
     * Define the form schema using standard sections.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Account Information'))
                    ->description(__('Update your account\'s profile information and email address.'))
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ])
                    ->columns(2),

                Section::make(__('Update Password'))
                    ->description(__('Ensure your account is using a long, random password to stay secure.'))
                    ->schema([
                        $this->getCurrentPasswordFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->autocomplete('new-password')
            ->extraInputAttributes([
                'autocomplete' => 'new-password',
                'data-lpignore' => 'true',
                'data-form-type' => 'other',
            ]);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->visible(true)
            ->required(fn (Get $get): bool => filled($get('password')))
            ->autocomplete('new-password')
            ->extraInputAttributes([
                'autocomplete' => 'new-password',
                'data-lpignore' => 'true',
                'data-form-type' => 'other',
            ]);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()
            ->visible(true)
            ->required(fn (Get $get): bool => filled($get('password')) || ($get('email') !== $this->getUser()->getAttributeValue('email')))
            ->autocomplete('one-time-code') // Often more effective than 'off' or 'current-password' for blocking autofill
            ->extraInputAttributes([
                'autocomplete' => 'one-time-code',
                'data-lpignore' => 'true',
                'data-form-type' => 'other',
            ]);
    }
}
