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

    private const FLAGS = [
        'registrationEnabled' => ['registration_enabled', 'การเปิดรับสมัครสมาชิกใหม่'],
        'storeEnabled' => ['store_enabled', 'ร้านค้า'],
        'classroomJoinEnabled' => ['classroom_join_enabled', 'การเข้าร่วมห้องเรียนใหม่'],
        'bugReportEnabled' => ['bug_report_enabled', 'ระบบรายงานปัญหา'],
    ];

    public function toggleFlag(string $prop): void
    {
        [$key, $label] = self::FLAGS[$prop];

        $this->$prop = ! $this->$prop;
        app(SettingsService::class)->set($key, $this->$prop);
        AuditLog::record('settings_changed', "สลับสถานะ{$label}เป็น ".($this->$prop ? 'เปิด' : 'ปิด'));
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
