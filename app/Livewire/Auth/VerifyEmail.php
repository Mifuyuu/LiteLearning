<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class VerifyEmail extends Component
{
    public string $status = '';

    public function mount(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard'), navigate: true);
        }
    }

    public function resend(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard'), navigate: true);

            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        $this->status = 'sent';
    }

    public function render()
    {
        return view('livewire.auth.verify');
    }
}
