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

    public function login()
    {
        $this->validate();

        // Rate-limit login attempts: 5 per minute per email+IP
        $throttleKey = strtolower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => true], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();

            /** @var User|null $user */
            $user = Auth::user();

            if ($user?->needsSetup()) {
                return redirect()->route('setup');
            }

            return redirect()->intended(route('dashboard'));
        }

        // Generic error message to prevent enumeration attacks
        $this->addError('password', __('The provided credentials do not match our records.'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
