<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Users;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_students_name_bio_coins_and_xp(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student', 'name' => 'Old Name']);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('updateUser', $student->username, 'New Name', 'New bio', 50, 200)
            ->assertDispatched('notify', message: __('messages.admin.user_updated'));

        $student->refresh();
        $this->assertSame('New Name', $student->name);
        $this->assertSame('New bio', $student->bio);
        $this->assertSame(50, $student->coins);
        $this->assertSame(200, $student->xp);
    }

    public function test_admin_can_update_a_teachers_name_and_bio_without_touching_coins(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Old Teacher']);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('updateUser', $teacher->username, 'New Teacher', 'Teaches things', 999, 999)
            ->assertDispatched('notify', message: __('messages.admin.user_updated'));

        $teacher->refresh();
        $this->assertSame('New Teacher', $teacher->name);
        $this->assertSame('Teaches things', $teacher->bio);
        $this->assertNull($teacher->gamification);
    }
}
