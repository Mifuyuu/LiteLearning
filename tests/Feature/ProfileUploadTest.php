<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'public']);
    }

    public function test_user_can_upload_cropped_avatar()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulated base64 avatar (small transparent dot)
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

        Livewire::test(\App\Livewire\Settings::class)
            ->set('avatar', $base64Image)
            ->call('uploadAvatar', $base64Image)
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringContainsString('avatars/', $user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_user_can_upload_cropped_cover()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulated base64 cover
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

        Livewire::test(\App\Livewire\Settings::class)
            ->set('cover_image', $base64Image)
            ->call('uploadCoverImage', $base64Image)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $user->refresh();
        $this->assertNotNull($user->cover_image);
        $this->assertStringContainsString('covers/', $user->cover_image);
        $this->assertTrue(Storage::disk('public')->exists($user->cover_image));
    }

    public function test_settings_upload_hides_internal_exception_details(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Settings::class)
            ->call('uploadAvatar', 'data:image/png;base64,invalid-data')
            ->assertDispatched(
                'notify',
                message: 'อัปโหลดล้มเหลว กรุณาลองอีกครั้ง',
                type: 'error'
            );
    }

    public function test_student_profile_renders_rank_graph_data()
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Livewire::test(\App\Livewire\Profile::class)
            ->assertViewHas('rank')
            ->assertViewHas('chartPoints')
            ->assertSee('อันดับ')
            ->assertDontSee('อันดับทั่วโลก')
            ->assertDontSee('อันดับในประเทศ');
    }

    public function test_teacher_profile_does_not_render_rank_graph_data()
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher);

        Livewire::test(\App\Livewire\Profile::class)
            ->assertViewHas('rank', null)
            ->assertDontSee('อันดับและแนวโน้มการเรียน');
    }
}
