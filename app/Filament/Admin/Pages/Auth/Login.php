<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\SendOtpCode;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public const OTP_EXPIRY_MINUTES = 2;

    public bool $otpSent = false;
    public ?string $email = null;

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            Action::make('resendOtp')
                ->label('Resend Code')
                ->link()
                ->color('gray')
                ->action(function () {
                    $user = User::where('email', $this->email)->first();
                    if ($user) {
                        $this->sendOtp($user);
                    }
                })
                ->visible(fn () => $this->otpSent),
            Action::make('back')
                ->label('Change Email')
                ->link()
                ->color('gray')
                ->action(function () {
                    $this->otpSent = false;
                    $this->email = null;
                    $this->form->fill();
                })
                ->visible(fn () => $this->otpSent),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('email_display')
                    ->label('Email Address')
                    ->content(fn () => new HtmlString("<strong>{$this->email}</strong>"))
                    ->visible(fn () => $this->otpSent),

                $this->getEmailFormComponent()
                    ->hidden(fn () => $this->otpSent),
                $this->getPasswordFormComponent()
                    ->hidden(fn () => $this->otpSent),
                $this->getRememberFormComponent()
                    ->hidden(fn () => $this->otpSent),

                TextInput::make('otp')
                    ->label('OTP Code')
                    ->required()
                    ->numeric()
                    ->placeholder('Enter the 6-digit code sent to your email')
                    ->autofocus()
                    ->visible(fn () => $this->otpSent),
            ])
            ->statePath('data');
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        if ($this->otpSent) {
            return $this->verifyOtp($data['otp']);
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.email' => 'These credentials do not match our records.',
            ]);
        }

        if (!$user->canAccessPanel(filament()->getCurrentPanel())) {
            throw ValidationException::withMessages([
                'data.email' => 'This account does not have access to this panel.',
            ]);
        }

        $this->email = $data['email'];
        $this->sendOtp($user);

        return null;
    }

    protected function sendOtp(User $user): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::updateOrCreate(
            ['email' => $user->email],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(static::OTP_EXPIRY_MINUTES),
            ]
        );

        try {
            $user->notify(new SendOtpCode($code));
        } catch (\Exception $e) {
            Notification::make()
                ->title('Email Error')
                ->body('Could not send OTP email. Please check your mail configuration.')
                ->danger()
                ->send();
            return;
        }

        $this->otpSent = true;

        Notification::make()
            ->title('OTP Sent')
            ->body('A verification code has been sent to your email.')
            ->success()
            ->send();

        $this->form->fill();
    }

    protected function verifyOtp(string $otp): ?LoginResponse
    {
        $otpCode = OtpCode::where('email', $this->email)
            ->where('code', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpCode) {
            throw ValidationException::withMessages([
                'data.otp' => 'The provided OTP is invalid or has expired.',
            ]);
        }

        $user = User::where('email', $this->email)->first();

        if ($user) {
            Auth::login($user, $this->data['remember'] ?? false);
            session()->regenerate();

            $otpCode->delete();

            return app(LoginResponse::class);
        }

        return null;
    }
}
