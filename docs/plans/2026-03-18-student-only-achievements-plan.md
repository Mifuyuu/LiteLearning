# Student-Only Achievements Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Make LiteLearning achievements consistently student-only in both runtime behavior and seeded data.

**Architecture:** Keep the existing student-only reward gate in `GamificationService`, remove dead teacher achievement unlock branches, and trim the seeder to student-oriented achievement codes only. Verify the change with focused gamification tests plus a seeder regression test so runtime behavior and seeded data stay aligned.

**Tech Stack:** Laravel 12, Livewire v4, PHPUnit, SQLite in-memory seed/test database

---

### Task 1: Add regression coverage for student-only achievements

**Files:**
- Modify: `tests/Feature/GamificationTest.php`
- Modify: `database/seeders/GamificationFeaturesSeeder.php`
- Modify: `app/Services/GamificationService.php`

**Step 1: Write the failing tests**

Add tests to `tests/Feature/GamificationTest.php` for both of these behaviors:

```php
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

public function test_gamification_seeder_removes_teacher_only_achievement_codes(): void
{
    Achievement::create([
        'code' => 'first_classroom_created',
        'name' => 'Legacy Teacher Achievement',
        'coin_reward' => 0,
        'xp_reward' => 0,
        'is_active' => true,
    ]);

    $this->seed(\Database\Seeders\GamificationFeaturesSeeder::class);

    $this->assertDatabaseMissing('achievements', [
        'code' => 'first_classroom_created',
    ]);
}
```

Keep the existing student unlock test in place so the suite proves the supported student path still works.

**Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Feature/GamificationTest.php
```

Expected: FAIL because the seeder still keeps teacher-only codes and/or the test expectations do not yet match current seeded behavior.

**Step 3: Write minimal implementation**

In `database/seeders/GamificationFeaturesSeeder.php`:
- remove `first_classroom_created`
- remove `classroom_builder`
- remove `first_assignment_created`
- update the stale-code cleanup list to match the remaining student-only achievement codes only

In `app/Services/GamificationService.php`:
- remove teacher achievement unlock branches from `awardForClassroomCreated()`
- remove teacher achievement unlock branch from `awardForAssignmentCreated()`
- leave the student eligibility gate in place unchanged

**Step 4: Run tests to verify they pass**

```bash
php artisan test tests/Feature/GamificationTest.php
```

Expected: PASS

**Step 5: Run focused verification**

```bash
php artisan test tests/Feature/GamificationTest.php tests/Feature/SecurityTest.php
```

Expected: PASS

**Step 6: Commit**

```bash
git add tests/Feature/GamificationTest.php app/Services/GamificationService.php database/seeders/GamificationFeaturesSeeder.php docs/plans/2026-03-18-student-only-achievements-design.md docs/plans/2026-03-18-student-only-achievements-plan.md
git commit -m "refactor: make achievements student only"
```
