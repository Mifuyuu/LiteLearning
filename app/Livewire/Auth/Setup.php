<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Setup extends Component
{
    public string $role = 'student';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->needsSetup()) {
            $this->redirectRoute('dashboard', navigate: true);
            return;
        }

        $this->role = $user->role;
    }

    public function completeSetup(): void
    {
        $this->validate([
            'role' => 'required|in:student,teacher',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'role' => $this->role,
            'locale' => app()->getLocale(),
            'setup_completed_at' => now(),
        ]);

        session()->flash('message', __('Setup completed successfully.'));
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.setup');
    }
}
