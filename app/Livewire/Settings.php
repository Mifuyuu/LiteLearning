<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    public string $locale = 'th';

    public function setLocale(string $value): void
    {
        $this->locale = $value;
        $this->updatedLocale($value);
    }

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();
        $this->locale = $user->locale ?? 'th';
    }

    public function updatedLocale(string $value)
    {
        if (!in_array($value, ['en', 'th'])) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();
        $user->update(['locale' => $value]);

        // Apply immediately
        App::setLocale($value);
        session()->put('locale', $value);

        session()->flash('message', __('Changes saved successfully.'));

        return $this->redirectRoute('settings', navigate: false);
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
