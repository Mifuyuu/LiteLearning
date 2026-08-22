<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Services\AdminAnalyticsService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SystemSettings extends Component
{
    public bool $registrationEnabled = true;

    public bool $storeEnabled = true;

    public bool $classroomJoinEnabled = true;

    public bool $bugReportEnabled = true;

    public int $xpPerLevelMultiplier = 100;

    public int $attendanceCoinReward = 50;

    public int $attendanceXpReward = 75;

    public int $bugReportRateLimit = 3;

    public int $classroomJoinRateLimit = 5;

    public function mount(): void
    {
        $settings = app(SettingsService::class);

        $this->registrationEnabled = $settings->bool('registration_enabled', true);
        $this->storeEnabled = $settings->bool('store_enabled', true);
        $this->classroomJoinEnabled = $settings->bool('classroom_join_enabled', true);
        $this->bugReportEnabled = $settings->bool('bug_report_enabled', true);

        $this->xpPerLevelMultiplier = $settings->int('xp_per_level_multiplier', 100);
        $this->attendanceCoinReward = $settings->int('attendance_coin_reward', 50);
        $this->attendanceXpReward = $settings->int('attendance_xp_reward', 75);
        $this->bugReportRateLimit = $settings->int('bug_report_rate_limit', 3);
        $this->classroomJoinRateLimit = $settings->int('classroom_join_rate_limit', 5);
    }

    public function toggleRegistration(): void
    {
        $this->registrationEnabled = ! $this->registrationEnabled;
        app(SettingsService::class)->set('registration_enabled', $this->registrationEnabled);
        AuditLog::record('settings_changed', 'สลับสถานะการเปิดรับสมัครสมาชิกใหม่เป็น '.($this->registrationEnabled ? 'เปิด' : 'ปิด'));
        $this->dispatch('notify', message: __('messages.admin.settings_updated'));
    }

    public function toggleStore(): void
    {
        $this->storeEnabled = ! $this->storeEnabled;
        app(SettingsService::class)->set('store_enabled', $this->storeEnabled);
        AuditLog::record('settings_changed', 'สลับสถานะร้านค้าเป็น '.($this->storeEnabled ? 'เปิด' : 'ปิด'));
        $this->dispatch('notify', message: __('messages.admin.settings_updated'));
    }

    public function toggleClassroomJoin(): void
    {
        $this->classroomJoinEnabled = ! $this->classroomJoinEnabled;
        app(SettingsService::class)->set('classroom_join_enabled', $this->classroomJoinEnabled);
        AuditLog::record('settings_changed', 'สลับสถานะการเข้าร่วมห้องเรียนใหม่เป็น '.($this->classroomJoinEnabled ? 'เปิด' : 'ปิด'));
        $this->dispatch('notify', message: __('messages.admin.settings_updated'));
    }

    public function toggleBugReport(): void
    {
        $this->bugReportEnabled = ! $this->bugReportEnabled;
        app(SettingsService::class)->set('bug_report_enabled', $this->bugReportEnabled);
        AuditLog::record('settings_changed', 'สลับสถานะระบบรายงานปัญหาเป็น '.($this->bugReportEnabled ? 'เปิด' : 'ปิด'));
        $this->dispatch('notify', message: __('messages.admin.settings_updated'));
    }

    public function saveGameConfig(): void
    {
        $this->validate([
            'xpPerLevelMultiplier' => 'required|integer|min:1',
            'attendanceCoinReward' => 'required|integer|min:0',
            'attendanceXpReward' => 'required|integer|min:0',
            'bugReportRateLimit' => 'required|integer|min:1',
            'classroomJoinRateLimit' => 'required|integer|min:1',
        ]);

        $settings = app(SettingsService::class);
        $settings->set('xp_per_level_multiplier', $this->xpPerLevelMultiplier);
        $settings->set('attendance_coin_reward', $this->attendanceCoinReward);
        $settings->set('attendance_xp_reward', $this->attendanceXpReward);
        $settings->set('bug_report_rate_limit', $this->bugReportRateLimit);
        $settings->set('classroom_join_rate_limit', $this->classroomJoinRateLimit);

        $this->dispatch('notify', message: __('messages.admin.settings_updated'));
    }

    public function enableMaintenance(): void
    {
        $secret = Str::random(24);
        Artisan::call('down', ['--secret' => $secret, '--retry' => 60]);
        AuditLog::record('maintenance_enabled', 'เปิดโหมดปิดปรับปรุงระบบ');
        $this->redirect('/'.$secret);
    }

    public function disableMaintenance(): void
    {
        Artisan::call('up');
        AuditLog::record('maintenance_disabled', 'ปิดโหมดปิดปรับปรุงระบบ');
        $this->dispatch('notify', message: __('messages.admin.maintenance_disabled'));
    }

    public function render()
    {
        $analytics = app(AdminAnalyticsService::class);

        return view('livewire.admin.system-settings', [
            'storageUsage' => $analytics->storageUsage(),
            'laravelVersion' => app()->version(),
            'phpVersion' => PHP_VERSION,
            'environment' => app()->environment(),
            'dbDriver' => config('database.default'),
            'cacheDriver' => config('cache.default'),
            'queueDriver' => config('queue.default'),
            'failedJobsCount' => DB::table('failed_jobs')->count(),
            'isDownForMaintenance' => app()->isDownForMaintenance(),
        ]);
    }
}
