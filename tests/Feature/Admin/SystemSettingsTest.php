<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\SystemSettings;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    // Never let this test invoke the real `artisan down` — it writes a real
    // storage/framework/down file shared with the local dev environment and
    // would take the actual site offline if left behind by a failed run.
    public function test_enable_maintenance_calls_artisan_down_without_running_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Artisan::shouldReceive('call')
            ->once()
            ->with('down', \Mockery::on(fn ($args) => ($args['--retry'] ?? null) === 60 && ! empty($args['--secret'])));

        Livewire::actingAs($admin)
            ->test(SystemSettings::class)
            ->call('enableMaintenance');
    }

    public function test_disable_maintenance_calls_artisan_up(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Artisan::shouldReceive('call')->once()->with('up');

        Livewire::actingAs($admin)
            ->test(SystemSettings::class)
            ->call('disableMaintenance')
            ->assertDispatched('notify', message: __('messages.admin.maintenance_disabled'));
    }

    public function test_toggling_a_feature_flag_persists_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(SystemSettings::class)
            ->call('toggleFlag', 'storeEnabled')
            ->assertSet('storeEnabled', false)
            ->assertDispatched('notify', message: __('messages.admin.settings_updated'));

        $this->assertFalse(app(SettingsService::class)->bool('store_enabled', true));
    }

    public function test_saving_game_config_persists_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(SystemSettings::class)
            ->set('attendanceCoinReward', 99)
            ->call('saveGameConfig')
            ->assertDispatched('notify', message: __('messages.admin.settings_updated'));

        $this->assertSame(99, app(SettingsService::class)->int('attendance_coin_reward'));
    }

    public function test_admin_can_export_database(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = Livewire::actingAs($admin)
            ->test(SystemSettings::class)
            ->call('exportDatabase');

        $response->assertFileDownloaded();
    }

    public function test_database_export_service_generates_valid_sql(): void
    {
        User::factory()->create(['name' => 'Database Export Test User']);

        $exporter = app(\App\Services\DatabaseExportService::class);

        ob_start();
        $exporter->dumpToOutput();
        $sqlOutput = ob_get_clean();

        $this->assertNotEmpty($sqlOutput);
        $this->assertStringContainsString('LiteLearning Database Backup', $sqlOutput);
        $this->assertStringContainsString('users', $sqlOutput);
        $this->assertStringContainsString('Database Export Test User', $sqlOutput);
    }
}
