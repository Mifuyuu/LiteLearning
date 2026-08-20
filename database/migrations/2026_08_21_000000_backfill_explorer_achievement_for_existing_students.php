<?php

use App\Models\Achievement;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Achievement definitions normally come from GamificationFeaturesSeeder, but
        // production deploys only run `migrate --force` (no seed step) — define them
        // here too so this backfill works standalone.
        Achievement::firstOrCreate(
            ['code' => 'explorer'],
            [
                'name' => 'ก้าวแรกสู่ระบบ',
                'description' => 'เข้าสู่ระบบครั้งแรกหลังสมัครสมาชิก',
                'badge_image' => 'images/achievements/Achievements_Explorer.png',
                'coin_reward' => 75,
                'xp_reward' => 100,
                'is_active' => true,
            ]
        );

        Achievement::firstOrCreate(
            ['code' => 'collector'],
            [
                'name' => 'นักสะสมความสำเร็จ',
                'description' => 'ปลดล็อกความสำเร็จอื่นครบทุกอย่าง',
                'badge_image' => 'images/achievements/Achievements_Collector.png',
                'coin_reward' => 350,
                'xp_reward' => 700,
                'is_active' => true,
            ]
        );

        // Every existing student already "entered the system for the first time"
        // long before this achievement existed — grandfather them in via the same
        // unlock path a fresh registration uses, so coin/xp rewards and the
        // Collector cascade (GamificationService::maybeUnlockCollector) apply
        // exactly as they would for a new user.
        $service = app(GamificationService::class);

        User::where('role', 'student')->each(function (User $user) use ($service): void {
            $service->awardForFirstLogin($user);
        });
    }

    public function down(): void
    {
        // Not reversible: unlocks granted here are indistinguishable from ones a
        // user could have earned normally after this migration ran, and reverting
        // would also claw back their coin/xp rewards. Left as a no-op by design.
    }
};
