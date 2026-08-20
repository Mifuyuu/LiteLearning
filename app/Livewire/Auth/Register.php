<?php

namespace App\Livewire\Auth;

use App\Models\EmailOtpVerification;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use App\Services\GamificationService;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('สมัครสมาชิก')]
#[Layout('layouts.guest')]
class Register extends Component
{
    // Step 1 — registration form
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'student';

    // Step 2 — OTP verification
    public string $otp = '';

    public bool $otpSent = false;

    public int $resendCooldown = 0;

    public function getIsTeacherProperty(): bool
    {
        return $this->role === 'teacher';
    }

    public function setIsTeacherProperty(bool $value): void
    {
        $this->role = $value ? 'teacher' : 'student';
    }

    private function registrationRules(): array
    {
        return [
            'name' => 'required|string|max:'.User::NAME_MAX_LENGTH,
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'role' => 'required|in:student,teacher',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('messages.validation.name'),
            'email.required' => __('messages.validation.email'),
            'password.required' => __('messages.validation.password'),
            'password_confirmation.required' => __('messages.validation.password_confirm'),
            'role.required' => __('messages.validation.role'),
        ];
    }

    public function register(): void
    {
        $this->validate($this->registrationRules());

        $this->sendOtp();
    }

    public function sendOtp(): void
    {
        $this->validate($this->registrationRules());

        $throttleKey = 'otp-resend:'.$this->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('otp', __('messages.auth.register_otp_fast', ['seconds' => $seconds]));

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailOtpVerification::where('email', $this->email)->delete();

        EmailOtpVerification::create([
            'email' => $this->email,
            'otp' => Hash::make($code),
            'user_data' => [
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ],
            'expires_at' => now()->addMinutes(10),
        ]);

        (new AnonymousNotifiable)
            ->route('mail', $this->email)
            ->notify(new SendOtpNotification($code));

        $this->otpSent = true;
        $this->resendCooldown = 60;
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => 'required|digits:6',
        ]);

        $throttleKey = 'otp-verify:'.$this->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('otp', __('messages.auth.register_otp_too_many', ['seconds' => $seconds]));

            return;
        }

        $record = EmailOtpVerification::where('email', $this->email)->latest()->first();

        if (! $record || $record->isExpired()) {
            $this->addError('otp', __('messages.auth.register_otp_expired'));
            $this->otpSent = false;

            return;
        }

        if (! Hash::check($this->otp, $record->otp)) {
            RateLimiter::hit($throttleKey, 300);
            $this->addError('otp', __('messages.auth.register_otp_wrong'));

            return;
        }

        RateLimiter::clear($throttleKey);

        $userData = $record->user_data;
        if (! in_array($userData['role'] ?? null, ['student', 'teacher'], true)) {
            $record->delete();
            $this->addError('role', __('messages.auth.register_role_invalid'));

            return;
        }

        $record->delete();

        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => $userData['role'],
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        app(GamificationService::class)->awardForFirstLogin($user);

        // No `navigate: true` here on purpose — the achievement-celebration modal
        // reads the unlocked-achievement session flash via Alpine's x-init on a
        // fresh page load. A wire:navigate soft transition doesn't reliably
        // re-trigger x-init, so the flash gets silently drained without ever
        // showing the modal (see JoinClassroom, which has the same constraint).
        $this->redirectRoute('dashboard');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register');
    }
}
