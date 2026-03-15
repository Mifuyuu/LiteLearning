# Expand CTI to Announcements & AttendanceSessions

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Fold `announcements` and `attendance_sessions` into the `classwork_items` CTI table, removing the separate `Announcement` model's direct `classroom_id`/`user_id`/`title`/`slug` columns and replacing the `AttendanceSession.assignment_id` FK with `classwork_item_id`.

**Architecture:** Extend the existing CTI pattern from Phase 1. `classwork_items.type` gains two new values: `'announcement'` and `'attendance'`. `Announcement` becomes a child model with a `classwork_item_id` FK (like `Assignment`/`Material`). `AttendanceSession` replaces its `assignment_id` FK with `classwork_item_id` (it's already a 1-to-1 child of an attendance-type classwork item). Route model binding, factories, and all Livewire components that currently touch `Announcement` directly must be updated.

**Tech Stack:** Laravel 12, Livewire v4, SQLite (tests), MySQL (prod), PSR-12 (Pint)

---

## Constraints (read before touching anything)

- **Never** `UPDATE table alias JOIN ... SET alias.col` — SQLite rejects it. Use correlated subqueries.
- **Never** `php artisan migrate:fresh`.
- **Never** suppress type errors (`as any`, `@ts-ignore`, `@ts-expect-error`).
- **Never** add `$guarded`. Use explicit `$fillable`.
- **Never** use `$casts` property — use `protected function casts(): array`.
- **No comments or docstrings** unless the code is a complex algorithm, security check, regex, or public API doc.
- **Do NOT modify test files.**
- All code must pass `php artisan test` (32 tests, all green).
- Run `./vendor/bin/pint` at the end.

---

## Current State

| Table | Columns relevant to this refactor |
|---|---|
| `announcements` | id, slug(16) nullable unique, user_id FK, classroom_id FK nullable, title nullable, text content, timestamps |
| `attendance_sessions` | id, slug(16) nullable unique, assignment_id FK, current_code(6) nullable, is_active bool, started_at datetime nullable, code_rotated_at datetime nullable, timestamps |
| `classwork_items` | id, type enum('assignment','material'), classroom_id FK, user_id FK, topic_id FK nullable, title(255), slug(32) unique, description longtext nullable, timestamps |

After this refactor:
- `classwork_items.type` expands to `('assignment','material','announcement','attendance')`
- `announcements` gains `classwork_item_id` FK, loses `user_id`, `classroom_id`, `title`, `slug`
- `attendance_sessions` gains `classwork_item_id` FK, loses `assignment_id`, `slug`

---

## Task 1: Write the migration

**Files:**
- Create: `database/migrations/2026_03_15_XXXXXX_expand_classwork_items_for_announcements_and_attendance.php`
  (use `php artisan make:migration expand_classwork_items_for_announcements_and_attendance` to generate the file)

**Step 1: Generate the migration file**

```bash
php artisan make:migration expand_classwork_items_for_announcements_and_attendance
```

**Step 2: Write `up()`**

The migration must, in order:

1. Alter `classwork_items.type` enum to include `'announcement'` and `'attendance'`.
   - MySQL: `DB::statement("ALTER TABLE classwork_items MODIFY COLUMN type ENUM('assignment','material','announcement','attendance') NOT NULL")`
   - SQLite (tests): `Schema::table` doesn't support modifying enums — use a `DB::statement` with a `PRAGMA` guard or a raw ALTER. Since SQLite stores enums as TEXT with a CHECK constraint that isn't enforced at the DB level, you can skip altering the column for SQLite compatibility. Use `DB::getDriverName()` to branch:
   ```php
   if (DB::getDriverName() !== 'sqlite') {
       DB::statement("ALTER TABLE classwork_items MODIFY COLUMN type ENUM('assignment','material','announcement','attendance') NOT NULL");
   }
   ```

2. Add `classwork_item_id` FK to `announcements` (nullable first):
   ```php
   Schema::table('announcements', function (Blueprint $table) {
       $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
   });
   ```

3. Add `classwork_item_id` FK to `attendance_sessions` (nullable first):
   ```php
   Schema::table('attendance_sessions', function (Blueprint $table) {
       $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
   });
   ```

4. Backfill `classwork_items` rows for existing announcements:
   ```php
   DB::statement("
       INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
       SELECT
           'announcement',
           a.classroom_id,
           a.user_id,
           NULL,
           COALESCE(a.title, ''),
           COALESCE(a.slug, ''),
           a.content,
           a.created_at,
           a.updated_at
       FROM announcements a
   ");
   ```
   Note: `description` in `classwork_items` stores the announcement `content`.

5. Set `announcements.classwork_item_id` via correlated subquery:
   ```php
   DB::statement("
       UPDATE announcements
       SET classwork_item_id = (
           SELECT id FROM classwork_items
           WHERE classwork_items.slug = announcements.slug
             AND classwork_items.type = 'announcement'
           LIMIT 1
       )
   ");
   ```

6. Backfill `classwork_items` rows for existing attendance_sessions (via their assignment's classwork_item):
   ```php
   DB::statement("
       INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
       SELECT
           'attendance',
           ci.classroom_id,
           ci.user_id,
           ci.topic_id,
           ci.title,
           COALESCE(att.slug, ''),
           ci.description,
           att.created_at,
           att.updated_at
       FROM attendance_sessions att
       JOIN assignments a ON a.id = att.assignment_id
       JOIN classwork_items ci ON ci.id = a.classwork_item_id
   ");
   ```

7. Set `attendance_sessions.classwork_item_id`:
   ```php
   DB::statement("
       UPDATE attendance_sessions
       SET classwork_item_id = (
           SELECT ci2.id FROM classwork_items ci2
           WHERE ci2.type = 'attendance'
             AND ci2.id NOT IN (SELECT classwork_item_id FROM attendance_sessions WHERE classwork_item_id IS NOT NULL)
             AND ci2.slug = (
                 SELECT COALESCE(att2.slug, '') FROM attendance_sessions att2 WHERE att2.id = attendance_sessions.id LIMIT 1
             )
           LIMIT 1
       )
   ");
   ```
   **Simpler alternative** (works because there's a 1-to-1 relationship via assignment):
   ```php
   DB::statement("
       UPDATE attendance_sessions
       SET classwork_item_id = (
           SELECT ci.id FROM classwork_items ci
           JOIN assignments a ON a.classwork_item_id = ci.id
           WHERE a.id = attendance_sessions.assignment_id
             AND ci.type = 'attendance'
           LIMIT 1
       )
   ");
   ```
   Use the simpler alternative.

8. Make `classwork_item_id` NOT NULL on both tables:
   ```php
   Schema::table('announcements', function (Blueprint $table) {
       $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
       $table->unique('classwork_item_id');
   });
   Schema::table('attendance_sessions', function (Blueprint $table) {
       $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
       $table->unique('classwork_item_id');
   });
   ```

9. Drop old indexes before dropping columns (SQLite requires this):
   ```php
   Schema::table('announcements', function (Blueprint $table) {
       $table->dropUnique(['slug']);
   });
   Schema::table('attendance_sessions', function (Blueprint $table) {
       $table->dropUnique(['slug']);
   });
   ```

10. Drop old columns from `announcements`:
    ```php
    Schema::table('announcements', function (Blueprint $table) {
        $table->dropConstrainedForeignId('user_id');
        $table->dropConstrainedForeignId('classroom_id');
        $table->dropColumn(['title', 'slug']);
    });
    ```
    Note: keep `content` — it stays on `announcements` as announcement-specific data.

11. Drop old columns from `attendance_sessions`:
    ```php
    Schema::table('attendance_sessions', function (Blueprint $table) {
        $table->dropConstrainedForeignId('assignment_id');
        $table->dropColumn(['slug']);
    });
    ```

**Step 3: Write `down()`** (reversal)

The `down()` must reverse all of the above:

1. Re-add columns to `announcements`:
   ```php
   Schema::table('announcements', function (Blueprint $table) {
       $table->foreignId('user_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
       $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
       $table->string('title')->nullable()->after('classroom_id');
       $table->string('slug', 16)->nullable()->after('title');
   });
   ```

2. Backfill `announcements` columns from `classwork_items`:
   ```php
   DB::statement('
       UPDATE announcements
       SET user_id = (SELECT user_id FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1),
           classroom_id = (SELECT classroom_id FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1),
           title = (SELECT title FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1),
           slug = (SELECT slug FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1)
   ');
   ```

3. Re-add unique index on `announcements.slug`:
   ```php
   Schema::table('announcements', function (Blueprint $table) {
       $table->unique('slug');
   });
   ```

4. Re-add columns to `attendance_sessions`:
   ```php
   Schema::table('attendance_sessions', function (Blueprint $table) {
       $table->foreignId('assignment_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
       $table->string('slug', 16)->nullable()->after('assignment_id');
   });
   ```

5. Backfill `attendance_sessions` columns:
   ```php
   DB::statement('
       UPDATE attendance_sessions
       SET slug = (SELECT slug FROM classwork_items WHERE classwork_items.id = attendance_sessions.classwork_item_id LIMIT 1),
           assignment_id = (
               SELECT a.id FROM assignments a
               JOIN classwork_items ci ON ci.id = a.classwork_item_id
               WHERE ci.type = \'attendance\'
                 AND ci.id = (
                   SELECT ci2.id FROM classwork_items ci2
                   WHERE ci2.id = attendance_sessions.classwork_item_id
                   LIMIT 1
                 )
               LIMIT 1
           )
   ');
   ```
   Wait — this is tricky. The attendance `ClassworkItem` doesn't have a direct link back to the Assignment. A cleaner approach for `down()`:
   - The attendance classwork_item was created from the assignment's classwork_item. Find the assignment whose classwork_item shares the same title/classroom/user with the attendance classwork_item, then set assignment_id to that assignment's id.
   - Actually the simplest: store nothing extra; just find the assignment whose ClassworkItem's classroom_id/user_id/title matches the attendance's ClassworkItem. This is ambiguous.
   - **Best approach for down()**: Skip backfilling `assignment_id` in down (set to NULL), just restore the column as nullable and add a note. For slug, restore from classwork_items.slug.
   ```php
   DB::statement('
       UPDATE attendance_sessions
       SET slug = (SELECT slug FROM classwork_items WHERE classwork_items.id = attendance_sessions.classwork_item_id LIMIT 1)
   ');
   ```

6. Re-add unique index on `attendance_sessions.slug`:
   ```php
   Schema::table('attendance_sessions', function (Blueprint $table) {
       $table->unique('slug');
   });
   ```

7. Drop `classwork_item_id` from both tables:
   ```php
   Schema::table('announcements', function (Blueprint $table) {
       $table->dropUnique(['classwork_item_id']);
       $table->dropConstrainedForeignId('classwork_item_id');
   });
   Schema::table('attendance_sessions', function (Blueprint $table) {
       $table->dropUnique(['classwork_item_id']);
       $table->dropConstrainedForeignId('classwork_item_id');
   });
   ```

8. Revert `classwork_items.type` enum (MySQL only):
   ```php
   if (DB::getDriverName() !== 'sqlite') {
       DB::statement("ALTER TABLE classwork_items MODIFY COLUMN type ENUM('assignment','material') NOT NULL");
   }
   ```

9. Delete the backfilled classwork_items rows:
   ```php
   DB::statement("DELETE FROM classwork_items WHERE type IN ('announcement', 'attendance')");
   ```

**Step 4: Run tests (should still pass with old models)**

```bash
php artisan test
```

Expected: 32 tests pass (models unchanged yet, migration adds new columns but doesn't break existing behavior).

---

## Task 2: Update `Announcement` model

**Files:**
- Modify: `app/Models/Announcement.php`

**Step 1: Update `$fillable` and add relationships**

New version:
```php
<?php

namespace App\Models;

use App\Models\Traits\HasCommentsAndAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasCommentsAndAttachments, HasFactory;

    protected $fillable = [
        'classwork_item_id',
        'content',
    ];

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

    public function getClassroomIdAttribute(): ?int
    {
        return $this->classworkItem?->classroom_id;
    }

    public function getUserIdAttribute(): ?int
    {
        return $this->classworkItem?->user_id;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->classworkItem?->title;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->classworkItem?->slug;
    }

    public function getClassroomAttribute(): ?Classroom
    {
        return $this->classworkItem?->classroom;
    }

    public function getUserAttribute(): ?User
    {
        return $this->classworkItem?->user;
    }
}
```

Note: `HasSlug` trait is **removed** (slug now lives on classwork_item). `BelongsToClassroom` trait is **removed** (classroom relationship proxied). Keep `HasCommentsAndAttachments` and `HasFactory`.

**Step 2: Update `ClassworkItem` model**

Add `announcement()` HasOne relationship:
```php
public function announcement(): HasOne
{
    return $this->hasOne(Announcement::class);
}
```

(It may already exist — check first. If so, skip.)

**Step 3: Run tests**

```bash
php artisan test
```

Expected: 32 tests pass.

---

## Task 3: Update `AttendanceSession` model

**Files:**
- Modify: `app/Models/AttendanceSession.php`

**Step 1: Update model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'classwork_item_id',
        'current_code',
        'is_active',
        'started_at',
        'code_rotated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'started_at' => 'datetime',
            'code_rotated_at' => 'datetime',
        ];
    }

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'classwork_item_id', 'classwork_item_id');
    }

    // generateNewCode(), isCodeExpired(), start(), stop() unchanged
}
```

The `assignment()` relationship uses a non-standard FK to keep `$assignment->attendanceSession` working. It joins via `attendance_sessions.classwork_item_id = assignments.classwork_item_id`.

**Step 2: Update `Assignment` model**

The `attendanceSession()` HasOne currently uses the default FK (`assignment_id`). Change it:
```php
public function attendanceSession(): HasOne
{
    return $this->hasOne(AttendanceSession::class, 'classwork_item_id', 'classwork_item_id');
}
```

**Step 3: Run tests**

```bash
php artisan test
```

Expected: 32 tests pass.

---

## Task 4: Update `ClassworkItem` model

**Files:**
- Modify: `app/Models/ClassworkItem.php`

Add `attendance()` HasOne (if not already present from Task 2's announcement addition):

```php
public function attendance(): HasOne
{
    return $this->hasOne(AttendanceSession::class);
}
```

Also ensure `announcement()` HasOne is present (added in Task 2 if not done already).

**Step 1: Check and update**

The full $fillable doesn't change. Only add the two HasOne relationships.

**Step 2: Run tests**

```bash
php artisan test
```

---

## Task 5: Update `Classroom` model — announcements relationship

**Files:**
- Modify: `app/Models/Classroom.php`

Change `announcements()` from `HasMany` to `HasManyThrough`:

```php
public function announcements(): HasManyThrough
{
    return $this->hasManyThrough(
        Announcement::class,
        ClassworkItem::class,
        'classroom_id',       // FK on classwork_items
        'classwork_item_id',  // FK on announcements
        'id',                 // local key on classrooms
        'id'                  // local key on classwork_items
    )->where('classwork_items.type', 'announcement')
     ->latest('classwork_items.created_at');
}
```

Remove the `->latest()` call that was on the old HasMany (it's now in the HasManyThrough above).

**Step 2: Run tests**

```bash
php artisan test
```

---

## Task 6: Update `User` model — announcements relationship

**Files:**
- Modify: `app/Models/User.php`

The `User::announcements()` is currently a direct `HasMany` via `announcements.user_id`. After the refactor, `announcements` no longer has `user_id`. Remove or update this relationship.

Check how it's used first:
```bash
grep -rn "->announcements\(\)\|user->announcements\|user()->announcements" app/ --include="*.php"
```

If it's only used internally and not in any Livewire/view code, remove the method.

If it IS used, change it to HasManyThrough:
```php
public function announcements(): HasManyThrough
{
    return $this->hasManyThrough(
        Announcement::class,
        ClassworkItem::class,
        'user_id',
        'classwork_item_id',
        'id',
        'id'
    )->where('classwork_items.type', 'announcement');
}
```

**Step 2: Run tests**

```bash
php artisan test
```

---

## Task 7: Update `Livewire/Classroom/Show.php`

**Files:**
- Modify: `app/Livewire/Classroom/Show.php`

**Step 1: Update `loadClassroomRelations()`**

The announcements query changes because `announcements.classroom_id` no longer exists. The relationship is now through `classwork_items`:

```php
// Load announcements with comments and users
$announcements = $this->classroom->announcements()->with(['classworkItem.user', 'comments.user'])->get();
$this->classroom->setRelation('announcements', $announcements);
```

Note: `->user` was accessed directly via proxy. With the new `HasManyThrough`, we eager-load `classworkItem.user` and the Blade template accesses `$announcement->user` (still works via proxy accessor).

**Step 2: Update `deleteAnnouncement()`**

Old code: `$announcement->classroom_id === $this->classroom->id`
New code: `$announcement->classworkItem->classroom_id === $this->classroom->id`

Or use the proxy: `$announcement->classroom_id === $this->classroom->id` (proxy accessor still returns classworkItem->classroom_id — so **this line doesn't need to change** if proxy accessor works).

Verify the proxy accessor is in place, then no change needed here.

**Step 3: Run tests**

```bash
php artisan test
```

---

## Task 8: Update `Livewire/Classroom/StreamComment.php`

**Files:**
- Modify: `app/Livewire/Classroom/StreamComment.php`

**Step 1: Update `addComment()`**

Old: `$announcement = Announcement::findOrFail($this->announcementId); $classroom = $announcement->classroom;`

The proxy accessor `$announcement->classroom` still works (it reads from `classworkItem->classroom`). So **no change needed here** if the proxy accessor is in place.

Double-check that `$announcement->classroom` returns the right value. If it does, this task is a no-op.

**Step 2: Run tests**

```bash
php artisan test
```

---

## Task 9: Update `Livewire/Assignment/Create.php`

**Files:**
- Modify: `app/Livewire/Assignment/Create.php`

Currently at lines 122-133, the announcement branch bypasses ClassworkItem entirely:
```php
if ($this->type === 'announcement') {
    Announcement::create([
        'user_id' => $user->id,
        'classroom_id' => $this->classroom->id,
        'title' => $this->title,
        'content' => $this->description ? Purifier::clean($this->description) : null,
    ]);
    ...
}
```

This must be unified into the ClassworkItem flow. Replace with:
```php
if ($this->type === 'announcement') {
    DB::transaction(function () use ($user, $topicId): void {
        $classworkItem = \App\Models\ClassworkItem::create([
            'type' => 'announcement',
            'classroom_id' => $this->classroom->id,
            'user_id' => $user->id,
            'topic_id' => $topicId,
            'title' => $this->title,
            'slug' => \App\Models\Traits\HasSlug::generateUniqueSlug($this->title),
            'description' => $this->description ? Purifier::clean($this->description) : null,
        ]);

        Announcement::create([
            'classwork_item_id' => $classworkItem->id,
            'content' => $this->description ? Purifier::clean($this->description) : null,
        ]);
    });

    $this->redirect(route('classroom.show', $this->classroom), navigate: true);
    return;
}
```

Also remove the `use App\Models\Announcement;` import if no longer used directly (it still is, so keep it).

**Step 2: Run tests**

```bash
php artisan test
```

---

## Task 10: Update `Livewire/Assignment/Attendance.php`

**Files:**
- Modify: `app/Livewire/Assignment/Attendance.php`

**Step 1: Update `startSession()`**

Old:
```php
$this->session = AttendanceSessionModel::create([
    'assignment_id' => $this->assignment->id,
    'is_active' => false,
]);
```

New — create classwork_item first, then AttendanceSession:
```php
if (! $this->session) {
    DB::transaction(function (): void {
        $assignmentItem = $this->assignment->classworkItem;
        $classworkItem = \App\Models\ClassworkItem::create([
            'type' => 'attendance',
            'classroom_id' => $assignmentItem->classroom_id,
            'user_id' => $assignmentItem->user_id,
            'topic_id' => $assignmentItem->topic_id,
            'title' => $assignmentItem->title,
            'slug' => \App\Models\Traits\HasSlug::generateUniqueSlug($assignmentItem->title),
            'description' => $assignmentItem->description,
        ]);

        $this->session = AttendanceSessionModel::create([
            'classwork_item_id' => $classworkItem->id,
            'is_active' => false,
        ]);
    });
}
```

Add `use Illuminate\Support\Facades\DB;` to imports.

**Step 2: Run tests**

```bash
php artisan test
```

---

## Task 11: Update `AnnouncementFactory`

**Files:**
- Modify: `database/factories/AnnouncementFactory.php`

New factory — uses ClassworkItem factory pattern like AssignmentFactory:

```php
<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\ClassworkItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    private const PARENT_KEYS = ['user_id', 'classroom_id', 'title', 'slug'];

    public function definition(): array
    {
        return [
            'classwork_item_id' => ClassworkItem::factory()->forAnnouncement(),
            'content' => fake()->paragraphs(rand(1, 3), true),
        ];
    }

    protected function expandAttributes(array $definition): array
    {
        [$parentAttrs, $childAttrs] = [[], []];

        foreach ($definition as $key => $value) {
            if (in_array($key, self::PARENT_KEYS, true)) {
                $parentAttrs[$key] = $value;
            } else {
                $childAttrs[$key] = $value;
            }
        }

        if (! empty($parentAttrs)) {
            $existing = $childAttrs['classwork_item_id'] ?? null;

            $childAttrs['classwork_item_id'] = $existing instanceof \Illuminate\Database\Eloquent\Factories\Factory
                ? $existing->state($parentAttrs)
                : ClassworkItem::factory()->forAnnouncement()->state($parentAttrs);
        }

        return parent::expandAttributes($childAttrs);
    }
}
```

**Step 2: Update `ClassworkItemFactory`**

Add `forAnnouncement()` and `forAttendance()` states:
```php
public function forAnnouncement(): static
{
    return $this->state(fn (array $attributes) => [
        'type' => 'announcement',
    ]);
}

public function forAttendance(): static
{
    return $this->state(fn (array $attributes) => [
        'type' => 'attendance',
    ]);
}
```

**Step 3: Run tests**

```bash
php artisan test
```

Expected: All 32 tests pass. The `ClassroomContentMigrationSafetyTest` uses `Announcement::factory()->create(['user_id' => ..., 'classroom_id' => ..., 'content' => ...])` — the `expandAttributes()` override will intercept `user_id` and `classroom_id` and forward them to the `ClassworkItem` factory.

---

## Task 12: Final verification

**Step 1: Run full test suite**

```bash
php artisan test
```

Expected: 32 tests pass, 0 failures.

**Step 2: Run Pint**

```bash
./vendor/bin/pint
```

Expected: no changes needed (or only whitespace/formatting fixes).

**Step 3: Run Pint check**

```bash
./vendor/bin/pint --test
```

Expected: exit 0.

**Step 4: Commit**

```bash
git add -A
git commit -m "refactor: expand classwork_items CTI to include announcements and attendance sessions"
```
