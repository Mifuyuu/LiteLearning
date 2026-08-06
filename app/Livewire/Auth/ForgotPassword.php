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

    protected $messages = [
        'email.exists' => 'ไม่พบอีเมลนี้ในระบบ',
        'otp.digits' => 'รหัส OTP ต้องเป็น 6 หลัก',
        'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
        'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
    ];

    public function submitEmail(): void
    {
        $this->validate();

        $throttleKey = 'forgot-password:' . $this->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'ส่งรหัสบ่อยเกินไป กรุณารอ ' . $seconds . ' วินาที');
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        /** @var User $user */
        $user = User::where('email', $this->email)->first();

        if (! $user->is_active) {
            $this->addError('email', 'บัญชีนี้ถูกปิดใช้งานแล้ว');
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailOtpVerification::where('email', $this->email)->delete();

        EmailOtpVerification::create([
            'email' => $this->email,
            'otp' => Hash::make($code),
            'user_data' => ['email' => $this->email],
            'expires_at' => now()->addMinutes(10),
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
            $this->addError('otp', 'ลองผิดบ่อยเกินไป กรุณารอ ' . $seconds . ' วินาที');
            return;
        }

        $record = EmailOtpVerification::where('email', $this->email)->latest()->first();

        if (! $record || $record->isExpired()) {
            $this->addError('otp', 'รหัสหมดอายุแล้ว กรุณาขอรหัสใหม่');
            $this->step = 1;
            $this->otp = '';
            return;
        }

        if (! Hash::check($this->otp, $record->otp)) {
            RateLimiter::hit($throttleKey, 300);
            $this->addError('otp', 'รหัสไม่ถูกต้อง');
            return;
        }

        RateLimiter::clear($throttleKey);
        $record->delete();

        $this->otp = '';
        $this->step = 3;
    }

    public function resetPassword(): void
    {
        $this->validate();

        /** @var User $user */
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', 'ไม่พบอีเมลนี้ในระบบ');
            $this->step = 1;
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        session()->flash('message', 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว');

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
