<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_frame_requires_an_uploaded_image_instead_of_a_manual_value(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Store::class)
            ->set('form.code', 'frame1')
            ->set('form.name', 'Frame')
            ->set('form.type', 'avatar_frame')
            ->set('form.value', '')
            ->call('save')
            ->assertHasErrors(['frameImageUpload' => 'required']);
    }

    public function test_avatar_frame_saves_with_an_uploaded_image(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Store::class)
            ->set('form.code', 'frame2')
            ->set('form.name', 'Frame')
            ->set('form.type', 'avatar_frame')
            ->set('frameImageUpload', UploadedFile::fake()->image('frame.png'))
            ->call('save')
            ->assertHasNoErrors();
    }
}
