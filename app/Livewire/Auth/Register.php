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
use Livewire\Component;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'role' => 'required|in:student,teacher',
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
            $this->addError('otp', 'ส่งรหัสบ่อยเกินไป กรุณารอ ' . $seconds . ' วินาที');

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
            $this->addError('otp', 'ลองผิดบ่อยเกินไป กรุณารอ ' . $seconds . ' วินาที');

            return;
        }

        $record = EmailOtpVerification::where('email', $this->email)->latest()->first();

        if (! $record || $record->isExpired()) {
            $this->addError('otp', 'รหัสหมดอายุแล้ว กรุณาขอรหัสใหม่');
            $this->otpSent = false;

            return;
        }

        if (! Hash::check($this->otp, $record->otp)) {
            RateLimiter::hit($throttleKey, 300);
            $this->addError('otp', 'รหัสไม่ถูกต้อง');

            return;
        }

        RateLimiter::clear($throttleKey);

        $userData = $record->user_data;
        if (! in_array($userData['role'] ?? null, ['student', 'teacher'], true)) {
            $record->delete();
            $this->addError('role', 'บทบาทไม่ถูกต้อง');

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

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register');
    }
}
