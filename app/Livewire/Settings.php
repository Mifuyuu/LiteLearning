<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    const NAME_MAX_LENGTH = 50;



    public string $name = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name;
    }



    public function updateName(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:'.self::NAME_MAX_LENGTH],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update(['name' => trim($this->name)]);
        $this->name = $user->name;

        session()->flash('message', __('Changes saved successfully.'));
        $this->redirectRoute('settings', navigate: false);
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
