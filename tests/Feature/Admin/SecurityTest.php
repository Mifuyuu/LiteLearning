<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Security;
use App\Livewire\Admin\SystemSettings;
use App\Livewire\Admin\Users;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabling_a_user_writes_an_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'student']);

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('toggleStatus', $target->username);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'user_disabled',
        ]);
    }

    public function test_toggling_maintenance_writes_an_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Artisan::shouldReceive('call')->once()->with('up');

        Livewire::actingAs($admin)
            ->test(SystemSettings::class)
            ->call('disableMaintenance');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'maintenance_disabled',
        ]);
    }

    public function test_admin_can_revoke_another_users_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create();

        DB::table('sessions')->insert([
            'id' => 'other-session-id',
            'user_id' => $target->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test-agent',
            'payload' => 'x',
            'last_activity' => now()->timestamp,
        ]);

        Livewire::actingAs($admin)
            ->test(Security::class)
            ->set('tab', 'sessions')
            ->call('revokeSession', 'other-session-id');

        $this->assertDatabaseMissing('sessions', ['id' => 'other-session-id']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'session_revoked']);
    }

    public function test_admin_cannot_revoke_their_own_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $component = Livewire::actingAs($admin)->test(Security::class);
        $currentSessionId = session()->getId();

        DB::table('sessions')->insert([
            'id' => $currentSessionId,
            'user_id' => $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test-agent',
            'payload' => 'x',
            'last_activity' => now()->timestamp,
        ]);

        $component->call('revokeSession', $currentSessionId);

        $this->assertDatabaseHas('sessions', ['id' => $currentSessionId]);
    }

    public function test_non_admin_cannot_access_security_page(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)->get(route('admin.security'))->assertForbidden();
    }
}
