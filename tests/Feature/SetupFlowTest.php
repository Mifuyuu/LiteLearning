<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SetupFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_setup_is_redirected_to_setup(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'setup_completed_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('setup'));
    }

    public function test_user_can_complete_setup(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'setup_completed_at' => null,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Auth\Setup::class)
            ->set('role', 'teacher')
            ->call('completeSetup')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'teacher',
        ]);
        
        $user->refresh();
        $this->assertNotNull($user->setup_completed_at);
    }
}
