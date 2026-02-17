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
            'tos_accepted_at' => null,
            'school_name' => null,
            'study_year' => null,
            'birth_date' => null,
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
            'tos_accepted_at' => null,
            'school_name' => null,
            'study_year' => null,
            'birth_date' => null,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Auth\Setup::class)
            ->set('role', 'teacher')
            ->set('school', 'Custom Institute')
            ->set('study_year', 'Other')
            ->set('study_year_other', 'Graduate Year 1')
            ->set('birth_date', now()->subYears(20)->format('Y-m-d'))
            ->set('accept_tos', true)
            ->call('completeSetup')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'teacher',
            'school_name' => 'Custom Institute',
            'study_year' => 'Graduate Year 1',
        ]);
    }
}
