<?php

namespace Tests\Feature;

use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_key_missing(): void
    {
        $settings = app(SettingsService::class);

        $this->assertSame('fallback', $settings->get('missing_key', 'fallback'));
        $this->assertTrue($settings->bool('missing_flag', true));
        $this->assertSame(42, $settings->int('missing_number', 42));
    }

    public function test_set_persists_and_is_readable_after_cache_bust(): void
    {
        $settings = app(SettingsService::class);

        $settings->set('store_enabled', false);
        $this->assertFalse($settings->bool('store_enabled', true));

        $this->assertDatabaseHas('settings', ['key' => 'store_enabled', 'value' => '0']);

        // simulate a fresh request re-reading from a cold cache
        Cache::forget('settings:all');
        $this->assertFalse($settings->bool('store_enabled', true));
    }

    public function test_set_invalidates_cache_immediately(): void
    {
        $settings = app(SettingsService::class);

        $settings->set('xp_per_level_multiplier', 100);
        $this->assertSame(100, $settings->int('xp_per_level_multiplier'));

        $settings->set('xp_per_level_multiplier', 200);
        $this->assertSame(200, $settings->int('xp_per_level_multiplier'));
    }
}
