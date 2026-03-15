# Scheduled Publish Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow teachers to schedule classwork to auto-publish at a future date/time, with the status flipping automatically via a Laravel scheduler command.

**Architecture:** Add `published_at` timestamp to `classwork_items`, add `scheduled` to `assignments.status` enum, add an Artisan command `classwork:publish-scheduled` that runs every minute and flips due assignments to published (creating submissions), and update the Create form to accept an optional `published_at` input.

**Tech Stack:** Laravel 12, Livewire v4, SQLite (tests), MySQL (prod), Laravel Scheduler

---

## Task 1: Migration — add `published_at` to `classwork_items` and extend `assignments.status`

**Files:**
- Create: `database/migrations/2026_03_15_210000_add_published_at_and_scheduled_status.php`

**Step 1: Create the migration file**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classwork_items', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('description');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('classwork_items', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->change();
        });
    }
};
```

> **Note on `status` change:** The original migration defines `status` as `ENUM('draft', 'published', 'closed')`. MySQL supports `CHANGE COLUMN` to extend an enum; SQLite (used in tests) does not support enums natively — it stores them as strings. The safest cross-DB approach is to change the column type to `string` (VARCHAR) and enforce values in application-level validation only. The existing `scopePublished` and status checks in code work the same way.

**Step 2: Run migration**

```bash
php artisan migrate
```
Expected: `2026_03_15_210000_add_published_at_and_scheduled_status ... DONE`

**Step 3: Update `ClassworkItem::$fillable`**

In `app/Models/ClassworkItem.php`, add `'published_at'` to `$fillable`:

```php
protected $fillable = [
    'type',
    'classroom_id',
    'user_id',
    'topic_id',
    'title',
    'slug',
    'description',
    'published_at',
];
```

Also add cast in `casts()` method (add the method if it doesn't exist):

```php
protected function casts(): array
{
    return [
        'published_at' => 'datetime',
    ];
}
```

**Step 4: Update `Assignment::$fillable`** — no change needed; `status` is already fillable.

Update `Assignment::casts()` to document valid status values (no code change needed, status is a string now).

**Step 5: Commit**

```bash
git add database/migrations/2026_03_15_210000_add_published_at_and_scheduled_status.php app/Models/ClassworkItem.php
git commit -m "feat: add published_at to classwork_items and extend assignment status for scheduling"
```

---

## Task 2: Artisan Command — `classwork:publish-scheduled`

**Files:**
- Create: `app/Console/Commands/PublishScheduledClasswork.php`
- Modify: `routes/console.php`

**Step 1: Write the failing test first**

In `tests/Feature/ScheduledPublishTest.php` (new file):

```php
<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduled_assignment_is_published_at_scheduled_time(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::create([
            'type' => 'assignment',
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Test Assignment',
            'slug' => 'test-assignment-sched',
            'description' => null,
            'published_at' => Carbon::now()->subMinute(),
        ]);

        $assignment = Assignment::create([
            'classwork_item_id' => $classworkItem->id,
            'max_score' => 100,
            'exp_reward' => 0,
            'coin_reward' => 0,
            'due_date' => null,
            'status' => 'scheduled',
            'type' => 'question',
            'allow_late_submission' => true,
        ]);

        $this->artisan('classwork:publish-scheduled')->assertExitCode(0);

        $assignment->refresh();
        $this->assertEquals('published', $assignment->status);
        $this->assertCount(1, $assignment->submissions);
        $this->assertEquals($student->id, $assignment->submissions->first()->user_id);
    }

    public function test_future_scheduled_assignment_is_not_published_yet(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        $classworkItem = ClassworkItem::create([
            'type' => 'assignment',
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Future Assignment',
            'slug' => 'future-assignment-sched',
            'description' => null,
            'published_at' => Carbon::now()->addHour(),
        ]);

        Assignment::create([
            'classwork_item_id' => $classworkItem->id,
            'max_score' => 100,
            'exp_reward' => 0,
            'coin_reward' => 0,
            'due_date' => null,
            'status' => 'scheduled',
            'type' => 'question',
            'allow_late_submission' => true,
        ]);

        $this->artisan('classwork:publish-scheduled')->assertExitCode(0);

        $this->assertDatabaseHas('assignments', [
            'classwork_item_id' => $classworkItem->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_duplicate_submissions_not_created(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::create([
            'type' => 'assignment',
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Dup Test',
            'slug' => 'dup-test-sched',
            'description' => null,
            'published_at' => Carbon::now()->subMinute(),
        ]);

        $assignment = Assignment::create([
            'classwork_item_id' => $classworkItem->id,
            'max_score' => 100,
            'exp_reward' => 0,
            'coin_reward' => 0,
            'due_date' => null,
            'status' => 'scheduled',
            'type' => 'question',
            'allow_late_submission' => true,
        ]);

        // Pre-existing submission (e.g. student was added after scheduling)
        $assignment->submissions()->create(['user_id' => $student->id, 'status' => 'assigned']);

        $this->artisan('classwork:publish-scheduled')->assertExitCode(0);

        $this->assertCount(1, $assignment->fresh()->submissions);
    }
}
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Feature/ScheduledPublishTest.php
```
Expected: All 3 tests FAIL with "Command not found" or similar.

**Step 3: Create the command**

```php
<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\ClassworkItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishScheduledClasswork extends Command
{
    protected $signature = 'classwork:publish-scheduled';
    protected $description = 'Publish classwork items whose scheduled publish time has arrived';

    public function handle(): int
    {
        $due = Assignment::where('status', 'scheduled')
            ->whereHas('classworkItem', function ($q) {
                $q->whereNotNull('published_at')
                  ->where('published_at', '<=', now());
            })
            ->with(['classworkItem.classroom.students'])
            ->get();

        foreach ($due as $assignment) {
            DB::transaction(function () use ($assignment): void {
                $assignment->update(['status' => 'published']);

                $existingStudentIds = $assignment->submissions()->pluck('user_id')->all();
                $students = $assignment->classworkItem->classroom->students ?? collect();

                foreach ($students as $student) {
                    if (in_array($student->id, $existingStudentIds, true)) {
                        continue;
                    }
                    $assignment->submissions()->create([
                        'user_id' => $student->id,
                        'status' => 'assigned',
                    ]);
                }
            });
        }

        return Command::SUCCESS;
    }
}
```

**Step 4: Register the command in the scheduler**

In `routes/console.php`, add:

```php
Schedule::command('classwork:publish-scheduled')->everyMinute();
```

**Step 5: Run tests to verify they pass**

```bash
php artisan test tests/Feature/ScheduledPublishTest.php
```
Expected: All 3 tests PASS.

**Step 6: Run full suite**

```bash
php artisan test
```
Expected: All tests pass (35 total including new 3).

**Step 7: Pint**

```bash
./vendor/bin/pint
```

**Step 8: Commit**

```bash
git add app/Console/Commands/PublishScheduledClasswork.php routes/console.php tests/Feature/ScheduledPublishTest.php
git commit -m "feat: add classwork:publish-scheduled command and register in scheduler"
```

---

## Task 3: Create Form — `published_at` input

**Files:**
- Modify: `app/Livewire/Assignment/Create.php`
- Modify: `resources/views/livewire/assignment/create.blade.php`

**Step 1: Add property and validation to `Create.php`**

Add property after `$due_date`:
```php
public ?string $published_at = null;
```

Update validation rule (add after `'due_date'`):
```php
'published_at' => 'nullable|date|after:now',
```

Update `$status` validation rule from:
```php
'status' => 'required|in:draft,published',
```
to:
```php
'status' => 'required|in:draft,published,scheduled',
```

Add status override logic in `save()` before the announcement/assignment branches (after the topic resolution block):
```php
if ($this->published_at && now()->lt(\Carbon\Carbon::parse($this->published_at))) {
    $this->status = 'scheduled';
}
```

In the `ClassworkItem::create([...])` calls (both announcement branch and assignment branch), add:
```php
'published_at' => $this->published_at ?: null,
```

For **assignments**, when `status = 'scheduled'`, do NOT create submissions immediately (the scheduler does that). The existing guard `if ($this->status === 'published' ...)` already handles this — no change needed there.

**Step 2: Add `published_at` to the view**

In `resources/views/livewire/assignment/create.blade.php`, locate the `due_date` input section and add a new input below it. Follow the exact same Tailwind/label pattern used by `due_date`.

Look for the `due_date` input block — it will look something like:
```html
<div ...>
    <label ...>{{ __('Due Date') }}</label>
    <input type="datetime-local" wire:model="due_date" ... />
</div>
```

Add below it:
```html
<div ...>
    <label ...>{{ __('Auto-Publish At') }}</label>
    <input type="datetime-local" wire:model="published_at" ... />
    <p class="...">{{ __('Leave blank to publish manually.') }}</p>
</div>
```

For announcements, materials, and attendance — the status toggle (draft/published) should be hidden or ignored when `published_at` is filled. Add Alpine.js conditional to hide the toggle:
```html
<div x-show="!$wire.published_at">
    <!-- existing draft/published status toggle -->
</div>
```

**Step 3: Run full test suite**

```bash
php artisan test
```
Expected: All tests pass.

**Step 4: Pint**

```bash
./vendor/bin/pint
```

**Step 5: Commit**

```bash
git add app/Livewire/Assignment/Create.php resources/views/livewire/assignment/create.blade.php
git commit -m "feat: add published_at input to classwork create form with scheduled status support"
```

---

## Task 4: Student Visibility — hide scheduled items in classroom stream

**Files:**
- Modify: `app/Livewire/Classroom/Show.php` (or wherever the stream query lives)
- Modify: `resources/views/livewire/classroom/show.blade.php` (teacher clock badge)

**Step 1: Locate the stream query**

In `app/Livewire/Classroom/Show.php`, find where `classwork_items` are loaded for the stream tab. It likely loads via `$this->classroom->classworkItems` or a query. Identify the query.

**Step 2: Add visibility scope**

For **students**, add a query scope to exclude items not yet visible:

```php
// In the stream query for students:
->where(function ($q) {
    $q->whereNull('classwork_items.published_at')
      ->orWhere('classwork_items.published_at', '<=', now());
})
// For assignments specifically, also exclude status = 'scheduled':
// (handled by joining assignments and checking status)
```

The simplest approach: after loading classwork items, filter in PHP for students:
```php
if ($user->isStudent()) {
    $items = $items->filter(function ($item) {
        if ($item->published_at && $item->published_at->isFuture()) {
            return false;
        }
        if ($item->type === 'assignment' && $item->assignment?->status === 'scheduled') {
            return false;
        }
        return true;
    });
}
```

**Step 3: Add teacher clock badge**

In the stream view, for each classwork item card, add a scheduled indicator visible only to teachers:

```html
@if (auth()->user()->isTeacher() && ($item->published_at?->isFuture() || $item->assignment?->status === 'scheduled'))
    <span class="...">
        <i class="fas fa-clock"></i>
        {{ $item->published_at?->format('d/m/Y H:i') }}
    </span>
@endif
```

**Step 4: Run full test suite**

```bash
php artisan test
```
Expected: All tests pass.

**Step 5: Pint**

```bash
./vendor/bin/pint
```

**Step 6: Commit**

```bash
git add app/Livewire/Classroom/Show.php resources/views/livewire/classroom/show.blade.php
git commit -m "feat: hide scheduled classwork from students, show clock badge for teachers"
```

---

## Final Verification

```bash
php artisan test
./vendor/bin/pint --test
```

All tests pass, no Pint violations.
