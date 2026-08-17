<?php

namespace App\Livewire\Auth;

use App\Models\EmailOtpVerification;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('ลืมรหัสผ่าน')]
#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    public int $step = 1; // 1=email, 2=otp, 3=new-password

    public string $email = '';

    public string $otp = '';

    public string $password = '';

    public string $password_confirmation = '';

    public int $resendCooldown = 0;

    protected function rules(): array
    {
        return match ($this->step) {
            1 => ['email' => 'required|email|exists:users,email'],
            2 => ['otp' => 'required|digits:6'],
            3 => [
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required',
            ],
            default => [],
        };
    }

    protected function messages(): array
    {
        return match ($this->step) {
            1 => ['email.exists' => __('messages.auth.forgot_email_not_found')],
            2 => ['otp.digits' => __('messages.auth.otp_digits')],
            3 => [
                'password.min' => __('messages.auth.password_min'),
                'password.confirmed' => __('messages.auth.password_mismatch'),
            ],
            default => [],
        };
    }

    public function clearFieldError(string $field): void
    {
        $this->resetErrorBag($field);
    }

    public function submitEmail(): void
    {
        $this->validate();

        $throttleKey = 'forgot-password:' . $this->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('messages.auth.forgot_otp_fast', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        /** @var User $user */
        $user = User::where('email', $this->email)->first();

        if (! $user->is_active) {
            $this->addError('email', __('messages.auth.forgot_account_disabled'));
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailOtpVerification::where('email', $this->email)->delete();

        EmailOtpVerification::create([
            'email' => $this->email,
            'otp' => Hash::make($code),
            'user_data' => ['email' => $this->email],
            'expires_at' => now()->addMinutes(5),
        ]);

        (new AnonymousNotifiable)
            ->route('mail', $this->email)
            ->notify(new SendOtpNotification($code));

        $this->step = 2;
        $this->resendCooldown = 60;
    }

    public function verifyOtp(): void
    {
        $this->validate();

        $throttleKey = 'forgot-password-verify:' . $this->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('otp', __('messages.auth.forgot_otp_too_many', ['seconds' => $seconds]));
            return;
        }

        $record = EmailOtpVerification::where('email', $this->email)->latest()->first();

        if (! $record || $record->isExpired()) {
            $this->addError('otp', __('messages.auth.forgot_otp_expired'));
            $this->step = 1;
            $this->otp = '';
            return;
        }

        if (! Hash::check($this->otp, $record->otp)) {
            RateLimiter::hit($throttleKey, 300);
            $this->addError('otp', __('messages.auth.forgot_otp_wrong'));
            return;
        }

        RateLimiter::clear($throttleKey);
        $record->delete();

        session()->put('pw_reset_expires_at', now()->addMinutes(5));

        $this->otp = '';
        $this->step = 3;
    }

    public function resetPassword(): void
    {
        $expiresAt = session('pw_reset_expires_at');

        if (! $expiresAt || $expiresAt->isPast()) {
            session()->forget('pw_reset_expires_at');
            $this->step = 1;
            $this->addError('email', __('messages.auth.forgot_reset_expired'));
            return;
        }

        $this->validate();

        /** @var User $user */
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', __('messages.auth.forgot_email_not_found'));
            $this->step = 1;
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        session()->forget('pw_reset_expires_at');

        Auth::login($user);

        session()->flash('message', __('messages.auth.forgot_password_reset'));

        $this->redirect(
            $user->isAdmin() ? route('admin.dashboard') : route('dashboard'),
            navigate: true,
        );
    }

    public function resendOtp(): void
    {
        $this->submitEmail();
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
