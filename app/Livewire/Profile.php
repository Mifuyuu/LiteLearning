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

    public function updatedAvatar($value)
    {
        $this->uploadAvatar($value);
    }

    public function updatedCoverImage($value)
    {
        $this->uploadCoverImage($value);
    }

    public function uploadAvatar($value)
    {
        if (is_string($value) && str_starts_with($value, 'data:image')) {
            $this->storeBase64Image($value, 'avatars', 'avatar');
        }
    }

    public function uploadCoverImage($value)
    {
        if (is_string($value) && str_starts_with($value, 'data:image')) {
            $this->storeBase64Image($value, 'covers', 'cover_image');
        }
    }

    protected function storeBase64Image(string $base64Data, string $folder, string $field)
    {
        try {
            // Parse the base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    throw new \Exception('Invalid image type.');
                }

                $imageData = base64_decode($base64Data);

                if ($imageData === false) {
                    throw new \Exception('Base64 decode failed.');
                }

                // C3: Limit decoded image size to 5MB
                if (strlen($imageData) > 5 * 1024 * 1024) {
                    throw new \Exception('Image is too large. Maximum size is 5MB.');
                }

                // S4: Verify image integrity
                $img = @imagecreatefromstring($imageData);
                if ($img === false) {
                    throw new \Exception('Invalid image data.');
                }
                imagedestroy($img);

                $fileName = $folder . '/' . uniqid() . '.' . $type;
                $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

                // Delete old file if exists
                if ($this->user->$field && Storage::disk($disk)->exists($this->user->$field)) {
                    Storage::disk($disk)->delete($this->user->$field);
                }

                Storage::disk($disk)->put($fileName, $imageData);
                
                $user = Auth::user();
                $user->update([$field => $fileName]);
                $this->user = $user->fresh();
                
                $this->$field = null;

                $message = $field === 'avatar' ? __('Profile picture updated successfully.') : __('Cover image updated successfully.');
                $this->dispatch('notify', message: $message);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Upload failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
