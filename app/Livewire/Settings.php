<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Settings extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.generic', ['pageTitle' => 'ตั้งค่า']);
    }
    const NAME_MAX_LENGTH = 50;

    public string $name = '';

    public $avatar = null;

    public $cover_image = null;

    public ?string $pendingReset = null;

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
        ], [
            'name.required' => __('messages.validation.name'),
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update(['name' => trim($this->name)]);
        $this->name = $user->name;

        $this->dispatch('notify', message: __('messages.profile.saved'));
    }

    public function updatedAvatar($value): void
    {
        $this->uploadAvatar($value);
    }

    public function updatedCoverImage($value): void
    {
        $this->uploadCoverImage($value);
    }

    public function uploadAvatar($value): void
    {
        if (is_string($value) && str_starts_with($value, 'data:image')) {
            $this->storeBase64Image($value, 'avatars', 'avatar');
        }
    }

    public function uploadCoverImage($value): void
    {
        if (is_string($value) && str_starts_with($value, 'data:image')) {
            $this->storeBase64Image($value, 'covers', 'cover_image');
        }
    }

    public function resetAvatar(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        if ($user->avatar && Storage::disk($disk)->exists($user->avatar)) {
            Storage::disk($disk)->delete($user->avatar);
        }
        $user->update(['avatar' => null]);
        $this->dispatch('notify', message: __('messages.profile.avatar_reset'));
    }

    public function resetCoverImage(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        if ($user->cover_image && Storage::disk($disk)->exists($user->cover_image)) {
            Storage::disk($disk)->delete($user->cover_image);
        }
        $user->update(['cover_image' => null]);
        $this->dispatch('notify', message: __('messages.profile.cover_reset'));
    }

    public function confirmReset(string $type): void
    {
        $this->pendingReset = $type;
    }

    public function doReset(): void
    {
        if ($this->pendingReset === 'avatar') {
            $this->resetAvatar();
        } elseif ($this->pendingReset === 'cover') {
            $this->resetCoverImage();
        }
        $this->pendingReset = null;
    }

    protected function storeBase64Image(string $base64Data, string $folder, string $field): void
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]);

                if (! in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    throw new \Exception('Invalid image type.');
                }

                $imageData = base64_decode($base64Data);

                if ($imageData === false) {
                    throw new \Exception('Base64 decode failed.');
                }

                if (strlen($imageData) > 5 * 1024 * 1024) {
                    throw new \Exception('Image is too large. Maximum size is 5MB.');
                }

                $img = @imagecreatefromstring($imageData);
                if ($img === false) {
                    throw new \Exception('Invalid image data.');
                }
                imagedestroy($img);

                $fileName = $folder.'/'.uniqid().'.'.$type;
                $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

                /** @var User $user */
                $user = Auth::user();
                if ($user->$field && Storage::disk($disk)->exists($user->$field)) {
                    Storage::disk($disk)->delete($user->$field);
                }

                Storage::disk($disk)->put($fileName, $imageData);

                $user->update([$field => $fileName]);

                $this->$field = null;

                $message = $field === 'avatar' ? __('messages.profile.avatar_updated') : __('messages.profile.cover_updated');
                $this->dispatch('notify', message: $message);
            }
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', message: __('messages.profile.upload_failed'), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
