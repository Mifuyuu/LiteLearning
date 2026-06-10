<?php

namespace Tests\Feature;

use App\Exceptions\GamificationException;
use App\Models\Achievement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\CoinTransaction;
use App\Models\Comment;
use App\Models\StoreItem;
use App\Models\Submission;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            'role' => 'student',
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
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->svc->awardCoins($teacher, 50, 'test');

        $this->assertNull($teacher->gamification()->first());
    }

    public function test_assignment_turn_in_reward_is_awarded_only_once(): void
    {
        $assignment = Assignment::factory()->create();

        $this->svc->awardForAssignmentTurnedIn($this->student, $assignment->id);
        $this->svc->awardForAssignmentTurnedIn($this->student, $assignment->id);

        $this->student->refresh();

        $this->assertSame(10, $this->student->coins);
        $this->assertSame(20, $this->student->xp);
        $this->assertDatabaseCount('coin_transactions', 1);
    }

    public function test_legacy_assignment_reward_prevents_duplicate_award(): void
    {
        $assignment = Assignment::factory()->create();
        $gamification = $this->student->gamification()->create([
            'coins' => 10,
            'xp' => 20,
            'level' => 1,
        ]);

        CoinTransaction::create([
            'user_id' => $this->student->id,
            'amount' => 10,
            'type' => 'earn',
            'source' => 'assignment_turned_in',
            'reference_type' => 'assignment',
            'reference_id' => $assignment->id,
            'happened_at' => now()->subDay(),
        ]);

        $this->svc->awardForAssignmentTurnedIn($this->student, $assignment->id);

        $gamification->refresh();

        $this->assertSame(10, $gamification->coins);
        $this->assertSame(20, $gamification->xp);
        $this->assertDatabaseCount('coin_transactions', 1);
    }

    public function test_idempotency_migration_backfills_legacy_event_reward(): void
    {
        $assignment = Assignment::factory()->create();
        $transaction = CoinTransaction::create([
            'user_id' => $this->student->id,
            'amount' => 10,
            'type' => 'earn',
            'source' => 'assignment_turned_in',
            'reference_type' => 'assignment',
            'reference_id' => $assignment->id,
            'happened_at' => now()->subDay(),
        ]);

        $migration = require database_path('migrations/2026_06_10_000000_add_idempotency_key_to_coin_transactions_table.php');
        $migration->down();
        $migration->up();

        $this->assertDatabaseHas('coin_transactions', [
            'id' => $transaction->id,
            'idempotency_key' => "assignment_turned_in:{$this->student->id}:{$assignment->id}",
        ]);
    }

    public function test_assignment_reward_rolls_back_when_xp_award_fails(): void
    {
        $assignment = Assignment::factory()->create();
        $service = new class extends GamificationService
        {
            public function awardXp(User $user, int $amount): void
            {
                throw new \RuntimeException('XP storage unavailable');
            }
        };

        try {
            $service->awardForAssignmentTurnedIn($this->student, $assignment->id);
            $this->fail('Expected XP failure to be thrown.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('XP storage unavailable', $exception->getMessage());
        }

        $this->assertDatabaseCount('coin_transactions', 0);
        $this->assertNull($this->student->gamification()->first());
    }

    public function test_classroom_join_reward_is_awarded_only_once(): void
    {
        $classroom = Classroom::factory()->create();

        $this->svc->awardForClassroomJoined($this->student, $classroom->id);
        $this->svc->awardForClassroomJoined($this->student, $classroom->id);

        $this->student->refresh();

        $this->assertSame(15, $this->student->coins);
        $this->assertSame(25, $this->student->xp);
        $this->assertDatabaseCount('coin_transactions', 1);
    }

    // ─────────────────────────────────────────────
    // Achievement Unlock Idempotency
    // ─────────────────────────────────────────────

    public function test_achievement_is_only_unlocked_once(): void
    {
        Achievement::create([
            'code' => 'first_test',
            'name' => 'First Test',
            'coin_reward' => 0,
            'xp_reward' => 0,
            'is_active' => true,
        ]);

        $this->svc->unlockAchievement($this->student, 'first_test');
        $this->svc->unlockAchievement($this->student, 'first_test'); // duplicate

        $this->assertEquals(1, $this->student->achievements()->count());
    }

    public function test_teacher_cannot_unlock_achievement(): void
    {
        Achievement::create([
            'code' => 'student_only_test',
            'name' => 'Student Only Test',
            'coin_reward' => 0,
            'xp_reward' => 0,
            'is_active' => true,
        ]);

        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->svc->unlockAchievement($teacher, 'student_only_test');

        $this->assertEquals(0, $teacher->achievements()->count());
    }

    public function test_gamification_seeder_keeps_only_student_achievement_codes(): void
    {
        $this->seed(\Database\Seeders\GamificationFeaturesSeeder::class);

        $this->assertEqualsCanonicalizing([
            'first_classroom_joined',
            'first_assignment_turned_in',
            'consistent_submitter',
            'perfect_score',
            'early_bird',
            'social_butterfly',
            'on_a_roll',
            'grade_seeker',
            'multi_class',
            'chatterbox',
            'level_up',
        ], Achievement::query()->pluck('code')->all());
    }

    public function test_assignment_activity_unlocks_submission_achievements(): void
    {
        $this->createAchievement('first_assignment_turned_in');
        $this->createAchievement('early_bird');
        $this->createAchievement('on_a_roll');

        /** @var Classroom $classroom */
        $classroom = Classroom::factory()->create();

        for ($index = 0; $index < 5; $index++) {
            $assignment = $this->createAssignmentForClassroom($classroom, [
                'due_date' => now()->addDays(2),
            ]);

            $assignment->submissions()->create([
                'user_id' => $this->student->id,
                'status' => 'turned_in',
                'turned_in_at' => now(),
            ]);
        }

        $latestAssignment = Assignment::query()->latest('id')->firstOrFail();
        $this->svc->awardForAssignmentTurnedIn($this->student, $latestAssignment->id);

        $this->assertStudentHasAchievements([
            'first_assignment_turned_in',
            'early_bird',
            'on_a_roll',
        ]);
    }

    public function test_completed_classroom_unlocks_consistent_submitter(): void
    {
        $this->createAchievement('consistent_submitter');

        /** @var Classroom $classroom */
        $classroom = Classroom::factory()->create();
        $classroom->members()->attach($this->student->id, [
            'role' => 'student',
            'joined_at' => now(),
        ]);

        $assignments = collect([
            $this->createAssignmentForClassroom($classroom),
            $this->createAssignmentForClassroom($classroom),
        ]);

        $assignments->each(function (Assignment $assignment): void {
            $assignment->submissions()->create([
                'user_id' => $this->student->id,
                'status' => 'turned_in',
                'turned_in_at' => now(),
            ]);
        });

        $this->svc->awardForAssignmentTurnedIn($this->student, $assignments->last()->id);

        $this->assertStudentHasAchievements(['consistent_submitter']);
    }

    public function test_joining_three_classrooms_unlocks_multi_class(): void
    {
        $this->createAchievement('multi_class');

        Classroom::factory()->count(3)->create()->each(function (Classroom $classroom): void {
            $classroom->members()->attach($this->student->id, [
                'role' => 'student',
                'joined_at' => now(),
            ]);
        });

        $latestClassroom = Classroom::query()->latest('id')->firstOrFail();
        $this->svc->awardForClassroomJoined($this->student, $latestClassroom->id);

        $this->assertStudentHasAchievements(['multi_class']);
    }

    public function test_comments_unlock_social_achievements(): void
    {
        $this->createAchievement('social_butterfly');
        $this->createAchievement('chatterbox');

        for ($index = 0; $index < 5; $index++) {
            $comment = Comment::create([
                'commentable_type' => User::class,
                'commentable_id' => $this->student->id,
                'user_id' => $this->student->id,
                'content' => 'Comment '.$index,
            ]);

            $this->svc->awardForCommentCreated($this->student, $comment->id);
        }

        $this->assertStudentHasAchievements([
            'social_butterfly',
            'chatterbox',
        ]);
    }

    public function test_grading_unlocks_score_achievements(): void
    {
        $this->createAchievement('perfect_score');
        $this->createAchievement('grade_seeker');

        /** @var Classroom $classroom */
        $classroom = Classroom::factory()->create();
        $latestSubmission = null;

        for ($index = 0; $index < 5; $index++) {
            $assignment = $this->createAssignmentForClassroom($classroom, [
                'max_score' => 10,
            ]);

            $latestSubmission = $assignment->submissions()->create([
                'user_id' => $this->student->id,
                'status' => 'graded',
                'score' => 10,
                'turned_in_at' => now()->subDay(),
                'graded_at' => now(),
            ]);
        }

        $this->assertInstanceOf(Submission::class, $latestSubmission);
        $this->svc->awardForSubmissionGraded($latestSubmission);

        $this->assertStudentHasAchievements([
            'perfect_score',
            'grade_seeker',
        ]);
    }

    public function test_reaching_level_five_unlocks_level_up_achievement(): void
    {
        $this->createAchievement('level_up');

        $this->svc->awardXp($this->student, $this->svc->totalXpForLevel(5));

        $this->assertStudentHasAchievements(['level_up']);
    }

    // ─────────────────────────────────────────────
    // Store Purchase
    // ─────────────────────────────────────────────

    public function test_student_can_purchase_item_with_enough_coins(): void
    {
        $this->svc->awardCoins($this->student, 100, 'test');

        $item = StoreItem::create([
            'code' => 'frame-gold',
            'name' => 'Test Frame',
            'type' => 'avatar_frame',
            'value' => 'frame-gold',
            'price' => 50,
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
            'code' => 'frame-diamond',
            'name' => 'Expensive Frame',
            'type' => 'avatar_frame',
            'value' => 'frame-diamond',
            'price' => 500,
            'is_active' => true,
        ]);

        $this->expectException(GamificationException::class);

        $this->svc->purchaseItem($this->student, $item);
    }

    public function test_duplicate_purchase_is_rejected(): void
    {
        $this->svc->awardCoins($this->student, 200, 'test');

        $item = StoreItem::create([
            'code' => 'color-red',
            'name' => 'Color',
            'type' => 'name_color',
            'value' => '#ff0000',
            'price' => 50,
            'is_active' => true,
        ]);

        $this->svc->purchaseItem($this->student, $item);

        $this->expectException(GamificationException::class);

        $this->svc->purchaseItem($this->student, $item); // second time
    }

    public function test_duplicate_purchase_does_not_deduct_coins_twice(): void
    {
        $this->svc->awardCoins($this->student, 200, 'test');

        $item = StoreItem::create([
            'code' => 'color-blue',
            'name' => 'Blue Color',
            'type' => 'name_color',
            'value' => '#0000ff',
            'price' => 50,
            'is_active' => true,
        ]);

        $this->svc->purchaseItem($this->student, $item);

        try {
            $this->svc->purchaseItem($this->student, $item);
            $this->fail('Expected duplicate purchase to throw.');
        } catch (GamificationException $exception) {
            $this->assertNotEmpty($exception->getMessage());
        }

        $gam = $this->student->gamification()->first();
        $this->assertEquals(150, $gam->coins);
    }

    public function test_store_equip_hides_internal_exception_details(): void
    {
        $item = StoreItem::create([
            'code' => 'safe-error-test',
            'name' => 'Safe Error Test',
            'type' => 'avatar_frame',
            'value' => 'frame-test',
            'price' => 0,
            'is_active' => true,
        ]);

        $service = \Mockery::mock(GamificationService::class);
        $service->shouldReceive('equipItem')
            ->once()
            ->andThrow(new \RuntimeException('SQLSTATE secret database detail'));
        $this->app->instance(GamificationService::class, $service);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Student\Store::class)
            ->call('equip', $item->id)
            ->assertDispatched(
                'notify',
                message: __('Unable to equip item. Please try again.'),
                type: 'error'
            );
    }

    private function createAchievement(string $code): Achievement
    {
        return Achievement::create([
            'code' => $code,
            'name' => str($code)->headline()->toString(),
            'coin_reward' => 0,
            'xp_reward' => 0,
            'is_active' => true,
        ]);
    }

    private function createAssignmentForClassroom(Classroom $classroom, array $attributes = []): Assignment
    {
        /** @var ClassworkItem $classworkItem */
        $classworkItem = ClassworkItem::factory()
            ->forAssignment()
            ->create([
                'classroom_id' => $classroom->id,
                'user_id' => $classroom->teacher_id,
            ]);

        return Assignment::factory()->create(array_merge([
            'classwork_item_id' => $classworkItem->id,
            'status' => 'published',
            'type' => 'question',
        ], $attributes));
    }

    private function assertStudentHasAchievements(array $codes): void
    {
        $this->assertEqualsCanonicalizing(
            $codes,
            $this->student->achievements()
                ->whereIn('code', $codes)
                ->pluck('code')
                ->all()
        );
    }
}
