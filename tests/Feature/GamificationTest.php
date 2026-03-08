<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Classroom;
use App\Models\StoreItem;
use App\Models\User;
use App\Models\UserGamification;
use App\Services\GamificationService;
use App\Exceptions\GamificationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    private GamificationService $svc;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = app(GamificationService::class);

        $this->student = User::factory()->create([
            'role'               => 'student',
            'setup_completed_at' => now(),
        ]);
    }

    // ─────────────────────────────────────────────
    // XP & Level Progression
    // ─────────────────────────────────────────────

    public function test_xp_is_awarded_and_level_increases(): void
    {
        // Level 2 requires 100 XP (totalXpForLevel(2) = 100)
        $this->svc->awardXp($this->student, 100);

        $gam = $this->student->gamification()->first();
        $this->assertEquals(100, $gam->xp);
        $this->assertEquals(2, $gam->level);
    }

    public function test_level_is_capped_at_100(): void
    {
        // Award massive XP — should not loop past level 100
        $this->svc->awardXp($this->student, 9_999_999);

        $gam = $this->student->gamification()->first();
        $this->assertEquals(100, $gam->level);
    }

    public function test_level_up_grants_bonus_coins(): void
    {
        // Level 2 at 100 XP should grant 20 coins bonus
        $this->svc->awardXp($this->student, 100);

        $gam = $this->student->gamification()->first();
        $this->assertGreaterThanOrEqual(20, $gam->coins);
    }

    // ─────────────────────────────────────────────
    // Coin Awarding
    // ─────────────────────────────────────────────

    public function test_coins_are_awarded_to_eligible_student(): void
    {
        $this->svc->awardCoins($this->student, 50, 'test');

        $gam = $this->student->gamification()->first();
        $this->assertEquals(50, $gam->coins);
    }

    public function test_coins_are_not_awarded_to_teacher(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher', 'setup_completed_at' => now()]);

        $this->svc->awardCoins($teacher, 50, 'test');

        $this->assertNull($teacher->gamification()->first());
    }

    // ─────────────────────────────────────────────
    // Achievement Unlock Idempotency
    // ─────────────────────────────────────────────

    public function test_achievement_is_only_unlocked_once(): void
    {
        Achievement::create([
            'code'       => 'first_test',
            'name'       => 'First Test',
            'coin_reward'=> 0,
            'xp_reward'  => 0,
            'is_active'  => true,
        ]);

        $this->svc->unlockAchievement($this->student, 'first_test');
        $this->svc->unlockAchievement($this->student, 'first_test'); // duplicate

        $this->assertEquals(1, $this->student->achievements()->count());
    }

    // ─────────────────────────────────────────────
    // Store Purchase
    // ─────────────────────────────────────────────

    public function test_student_can_purchase_item_with_enough_coins(): void
    {
        $this->svc->awardCoins($this->student, 100, 'test');

        $item = StoreItem::create([
            'code'      => 'frame-gold',
            'name'      => 'Test Frame',
            'type'      => 'avatar_frame',
            'value'     => 'frame-gold',
            'price'     => 50,
            'is_active' => true,
        ]);

        $this->svc->purchaseItem($this->student, $item);

        $this->assertTrue(
            $this->student->storeItems()->where('store_item_id', $item->id)->exists()
        );

        $gam = $this->student->gamification()->first();
        $this->assertEquals(50, $gam->coins); // 100 - 50
    }

    public function test_purchase_fails_with_insufficient_coins(): void
    {
        $item = StoreItem::create([
            'code'      => 'frame-diamond',
            'name'      => 'Expensive Frame',
            'type'      => 'avatar_frame',
            'value'     => 'frame-diamond',
            'price'     => 500,
            'is_active' => true,
        ]);

        $this->expectException(GamificationException::class);

        $this->svc->purchaseItem($this->student, $item);
    }

    public function test_duplicate_purchase_is_rejected(): void
    {
        $this->svc->awardCoins($this->student, 200, 'test');

        $item = StoreItem::create([
            'code'      => 'color-red',
            'name'      => 'Color',
            'type'      => 'name_color',
            'value'     => '#ff0000',
            'price'     => 50,
            'is_active' => true,
        ]);

        $this->svc->purchaseItem($this->student, $item);

        $this->expectException(GamificationException::class);

        $this->svc->purchaseItem($this->student, $item); // second time
    }
}
