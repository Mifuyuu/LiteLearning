<?php

namespace Tests\Feature;

use App\Exceptions\GamificationException;
use App\Livewire\Auth\Register;
use App\Livewire\Classroom\JoinClassroom;
use App\Livewire\ReportBug;
use App\Livewire\Student\Store as StudentStore;
use App\Models\Classroom;
use App\Models\StoreItem;
use App\Models\User;
use App\Services\GamificationService;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SystemFeatureFlagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_blocked_when_disabled(): void
    {
        app(SettingsService::class)->set('registration_enabled', false);

        Livewire::test(Register::class)
            ->set('name', 'Somchai')
            ->set('email', 'somchai@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('email');

        $this->assertDatabaseMissing('users', ['email' => 'somchai@example.com']);
    }

    public function test_store_purchase_is_blocked_when_disabled(): void
    {
        app(SettingsService::class)->set('store_enabled', false);

        $student = User::factory()->create(['role' => 'student']);
        $item = StoreItem::create([
            'code' => 'test-item',
            'name' => 'Test Item',
            'description' => 'desc',
            'type' => 'name_color',
            'value' => 'text-red-500',
            'price' => 0,
            'is_active' => true,
        ]);

        $this->expectException(GamificationException::class);
        app(GamificationService::class)->purchaseItem($student, $item);
    }

    public function test_store_purchase_ui_shows_error_when_disabled(): void
    {
        app(SettingsService::class)->set('store_enabled', false);

        $student = User::factory()->create(['role' => 'student']);
        $item = StoreItem::create([
            'code' => 'test-item-2',
            'name' => 'Test Item 2',
            'description' => 'desc',
            'type' => 'name_color',
            'value' => 'text-red-500',
            'price' => 0,
            'is_active' => true,
        ]);

        Livewire::actingAs($student)
            ->test(StudentStore::class)
            ->call('purchase', $item->id)
            ->assertDispatched('notify', message: __('app.store_disabled'), type: 'error');
    }

    public function test_classroom_join_is_blocked_when_disabled(): void
    {
        app(SettingsService::class)->set('classroom_join_enabled', false);

        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        Livewire::actingAs($student)
            ->test(JoinClassroom::class)
            ->set('code', $classroom->code)
            ->call('join')
            ->assertHasErrors('code');

        $this->assertFalse($classroom->fresh()->students->contains($student));
    }

    public function test_bug_report_submission_is_blocked_when_disabled(): void
    {
        app(SettingsService::class)->set('bug_report_enabled', false);

        $student = User::factory()->create(['role' => 'student']);

        Livewire::actingAs($student)
            ->test(ReportBug::class)
            ->set('title', 'Something broke')
            ->set('message', 'It broke badly')
            ->call('submit')
            ->assertDispatched('notify', message: __('messages.report.closed'), type: 'error');

        $this->assertDatabaseMissing('bug_reports', ['title' => 'Something broke']);
    }
}
