<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    protected function messages(): array
    {
        return [
            'email.required' => __('messages.validation.email'),
            'password.required' => __('messages.validation.password'),
        ];
    }

    public function login()
    {
        $this->validate();

        // Rate-limit login attempts: 5 per minute per email+IP
        $throttleKey = strtolower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('messages.auth.login_throttle', ['seconds' => $seconds]));

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => true], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();

            /** @var User|null $user */
            $user = Auth::user();

            $defaultRoute = $user?->isAdmin()
                ? route('admin.dashboard')
                : route('dashboard');

            return redirect()->intended($defaultRoute);
        }

        // Generic error message to prevent enumeration attacks
        $this->addError('password', __('messages.auth.login_failed'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
