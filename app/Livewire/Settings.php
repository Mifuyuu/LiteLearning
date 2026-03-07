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

    public int $uiScale = 100;
    public string $name = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->uiScale = $user->ui_scale ?? 100;
        $this->name = $user->name;
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
