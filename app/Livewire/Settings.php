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
    public int $uiScale = 100;
    public string $name = '';

    const NAME_MAX_LENGTH = 50;

    public function setLocale(string $value): void
    {
        $this->locale = $value;
        $this->updatedLocale($value);
    }

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();
        $this->locale  = $user->locale ?? 'th';
        $this->uiScale = (int) ($user->ui_scale ?? 100);
        $this->name    = $user->name;
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

    public function setUiScale(string|int $value): void
    {
        $scale = (int) $value;
        if (!in_array($scale, [90, 100, 110, 125], true)) {
            return;
        }

        $this->uiScale = $scale;

        /** @var User $user */
        $user = Auth::user();
        $user->update(['ui_scale' => $scale]);

        session()->flash('message', __('Changes saved successfully.'));

        $this->redirectRoute('settings', navigate: false);
    }

    public function updateName(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:' . self::NAME_MAX_LENGTH],
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
