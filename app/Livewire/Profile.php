<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Profile extends Component
{
    use WithFileUploads;

    public $user;
    public $avatar;
    public $cover_image;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:2048',
        ]);

        if ($this->user->avatar && Storage::disk('public')->exists($this->user->avatar)) {
            Storage::disk('public')->delete($this->user->avatar);
        }

        $path = $this->avatar->store('avatars', 'public');
        $this->user->update(['avatar' => $path]);
        
        $this->avatar = null;
        
        session()->flash('message', __('Profile picture updated successfully.'));
    }

    public function updatedCoverImage()
    {
        $this->validate([
            'cover_image' => 'image|max:5120',
        ]);

        if ($this->user->cover_image && Storage::disk('public')->exists($this->user->cover_image)) {
            Storage::disk('public')->delete($this->user->cover_image);
        }

        $path = $this->cover_image->store('covers', 'public');
        $this->user->update(['cover_image' => $path]);
        
        $this->cover_image = null;

        session()->flash('message', __('Cover image updated successfully.'));
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
