<?php

namespace Tests\Feature;

use App\Exceptions\GamificationException;
use App\Livewire\Auth\Register;
use App\Models\Achievement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\CoinTransaction;
use App\Models\Comment;
use App\Models\EmailOtpVerification;
use App\Models\StoreItem;
use App\Models\Submission;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

        // Celebration flashes only queue for the user making the request
        // (see GamificationService::isCurrentUser) — most of this suite
        // exercises the student's own self-triggered actions.
        $this->actingAs($this->student);
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

    public function test_xp_award_that_levels_up_queues_a_celebration_flash(): void
    {
        $this->svc->awardXp($this->student, 100);

        $queued = session('new_level_ups');
        $this->assertCount(1, $queued);
        $this->assertSame(1, $queued[0]['level_before']);
        $this->assertSame(2, $queued[0]['level_after']);
    }

    public function test_big_xp_award_queues_one_celebration_per_level_gained(): void
    {
        // totalXpForLevel: 2=100, 3=300, 4=600 — 600 XP in one shot crosses 3 levels
        $this->svc->awardXp($this->student, 600);

        $queued = session('new_level_ups');
        $this->assertCount(3, $queued);
        $this->assertSame([1, 2], [$queued[0]['level_before'], $queued[0]['level_after']]);
        $this->assertSame([2, 3], [$queued[1]['level_before'], $queued[1]['level_after']]);
        $this->assertSame([3, 4], [$queued[2]['level_before'], $queued[2]['level_after']]);
    }

    public function test_xp_award_by_someone_else_queues_a_pending_celebration_instead_of_a_session_flash(): void
    {
        // Teacher grading a submission that levels up the student — the level-up
        // must not flash into the teacher's own session (see isCurrentUser()).
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher);

        $this->svc->awardXp($this->student, 100);

        $this->assertNull(session('new_level_ups'));

        $pending = $this->student->gamification()->first()->pending_celebrations;
        $this->assertCount(1, $pending['new_level_ups']);
        $this->assertSame(1, $pending['new_level_ups'][0]['level_before']);
        $this->assertSame(2, $pending['new_level_ups'][0]['level_after']);
    }

    public function test_pending_celebration_is_shown_and_cleared_on_the_students_own_next_page_load(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher);
        $this->svc->awardXp($this->student, 100); // levels the student up while the teacher is acting

        $this->actingAs($this->student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('"level_after":2');

        $this->assertNull($this->student->gamification()->first()->pending_celebrations);
    }

    public function test_xp_award_that_does_not_level_up_does_not_queue_a_celebration(): void
    {
        $this->svc->awardXp($this->student, 50); // level 2 needs 100 XP

        $this->assertNull(session('new_level_ups'));
    }

    public function test_level_is_capped_at_100(): void
    {
        // Award massive XP — should not loop past level 100
        $this->svc->awardXp($this->student, 9_999_999);

        $gam = $this->student->gamification()->first();
        $this->assertEquals(100, $gam->level);
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

    public function test_assignment_turn_in_does_not_hand_out_coins_or_xp(): void
    {
        $assignment = Assignment::factory()->create();

        $this->svc->awardForAssignmentTurnedIn($this->student, $assignment->id);

        $this->student->refresh();

        $this->assertSame(0, $this->student->coins);
        $this->assertSame(0, $this->student->xp);
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

    public function test_classroom_join_does_not_hand_out_coins_or_xp(): void
    {
        $classroom = Classroom::factory()->create();

        $this->svc->awardForClassroomJoined($this->student, $classroom->id);

        $this->student->refresh();

        $this->assertSame(0, $this->student->coins);
        $this->assertSame(0, $this->student->xp);
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

    public function test_first_login_unlocks_explorer_achievement(): void
    {
        // 'explorer' already exists — the 2026_08_21_000000 migration backfills it.

        $this->svc->awardForFirstLogin($this->student);

        $this->assertEquals(1, $this->student->achievements()->where('code', 'explorer')->count());
    }

    public function test_registration_completes_without_navigate_so_the_achievement_modal_can_show(): void
    {
        // wire:navigate soft-transitions don't reliably re-run the Alpine x-init
        // that reads the achievement-unlock session flash on the next page — a
        // hard redirect is required for the celebration modal to actually show
        // (see the comment in Register::verifyOtp()). Lock in that this redirect
        // never regains `navigate: true`.
        EmailOtpVerification::create([
            'email' => 'newstudent@example.com',
            'otp' => Hash::make('123456'),
            'user_data' => [
                'name' => 'New Student',
                'email' => 'newstudent@example.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
            ],
            'expires_at' => now()->addMinutes(10),
        ]);

        Livewire::test(Register::class)
            ->set('email', 'newstudent@example.com')
            ->set('otp', '123456')
            ->call('verifyOtp')
            ->assertRedirect(route('dashboard'));

        $newUser = User::where('email', 'newstudent@example.com')->firstOrFail();

        $this->assertEquals(1, $newUser->achievements()->where('code', 'explorer')->count());

        // The redirecting request must NOT have consumed the flash — it has to
        // survive for the fresh page load's x-init to pick up.
        $this->assertNotEmpty(session('new_achievements'));
    }

    public function test_collector_achievement_unlocks_only_after_all_other_achievements(): void
    {
        // 'explorer' and 'collector' already exist from the backfill migration —
        // 'explorer' counts as one of the "other" achievements to collect too.
        $this->createAchievement('a_one');
        $this->createAchievement('a_two');

        $this->svc->unlockAchievement($this->student, 'explorer');
        $this->svc->unlockAchievement($this->student, 'a_one');
        $this->assertEquals(0, $this->student->achievements()->where('code', 'collector')->count());

        $this->svc->unlockAchievement($this->student, 'a_two');
        $this->assertEquals(1, $this->student->achievements()->where('code', 'collector')->count());
    }

    public function test_collector_does_not_self_unlock_with_no_other_achievements(): void
    {
        // Deactivate the backfilled 'explorer' achievement so this test can exercise
        // the true "no other active achievements" edge case.
        Achievement::where('code', '!=', 'collector')->update(['is_active' => false]);

        $this->svc->unlockAchievement($this->student, 'collector');

        // Unlocking 'collector' itself must not recurse into its own requirement check.
        $this->assertEquals(1, $this->student->achievements()->count());
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
            'multi_class',
            'chatterbox',
            'level_up',
            'explorer',
            'collector',
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

        $this->assertFalse(
            (bool) $this->student->storeItems()->where('store_item_id', $item->id)->first()->pivot->is_active,
            'Purchasing an item must not equip it automatically.'
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
            ->test(\App\Livewire\Student\Inventory::class)
            ->call('equip', $item->id)
            ->assertDispatched(
                'notify',
                message: 'ไม่สามารถสวมใส่ไอเทมได้ กรุณาลองอีกครั้ง',
                type: 'error'
            );
    }

    public function test_admin_can_set_coins_and_xp_for_student(): void
    {
        $this->svc->awardCoins($this->student, 100, 'seed');

        $this->svc->adminSetCoinsAndXp($this->student, 500, 100);

        $gam = $this->student->gamification()->first();
        $this->assertSame(500, $gam->coins);
        $this->assertSame(100, $gam->xp);
        $this->assertSame(2, $gam->level);

        $this->assertSame(400, CoinTransaction::where('user_id', $this->student->id)
            ->where('source', 'admin_adjustment')
            ->value('amount'));
    }

    public function test_admin_cannot_set_coins_and_xp_for_non_student(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->expectException(GamificationException::class);
        $this->svc->adminSetCoinsAndXp($teacher, 100, 100);
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
            'type' => 'file',
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
