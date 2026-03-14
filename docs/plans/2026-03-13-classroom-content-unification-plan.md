# Classroom Content Unification Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Introduce a unified `ClassroomContent` root model and schema for assignments, materials, announcements, and attendance while preserving specialized child tables for submissions and attendance runtime state.

**Architecture:** Add a new `classroom_contents` root table, migrate existing roots into it in phases, and move application reads/writes incrementally. Keep `submissions` and `attendance_sessions` as specialized tables because they represent different lifecycles and cardinalities from top-level content.

**Tech Stack:** Laravel 12, Livewire v4, Eloquent models, SQLite feature tests, Laravel migrations, Blade views.

---

### Task 1: Lock in the current behavior with tests

**Files:**
- Create: `tests/Feature/ClassroomContentMigrationSafetyTest.php`
- Modify: `tests/Feature/AssignmentTest.php`
- Modify: `tests/Feature/SecurityTest.php`

**Step 1: Write the failing tests**

Add tests that describe current behavior that must survive the migration:

- classroom stream shows assignments/materials/announcements in the expected classroom only
- assignment submission remains tied to the correct classroom
- attendance check-in rejects content from another classroom with `404`

**Step 2: Run tests to verify current baseline**

Run: `php artisan test tests/Feature/ClassroomContentMigrationSafetyTest.php`

Expected: either PASS for existing behavior or FAIL because the new safety net does not exist yet.

**Step 3: Add any missing test fixtures/factories needed for mixed content setup**

Use existing factories for `User`, `Classroom`, `Assignment`, and create minimal fixtures for `Material` and `Announcement` if factory coverage is incomplete.

**Step 4: Run the focused test file again**

Run: `php artisan test tests/Feature/ClassroomContentMigrationSafetyTest.php`

Expected: PASS and establish migration safety.

**Step 5: Commit**

```bash
git add tests/Feature/ClassroomContentMigrationSafetyTest.php tests/Feature/AssignmentTest.php tests/Feature/SecurityTest.php
git commit -m "test: lock in content migration safety behavior"
```

### Task 2: Normalize topics before root-table unification

**Files:**
- Create: `database/migrations/2026_03_13_000001_add_topic_id_to_assignments.php`
- Modify: `app/Models/Assignment.php`
- Modify: `app/Livewire/Assignment/Create.php`
- Modify: `app/Livewire/Assignment/Show.php`
- Test: `tests/Feature/AssignmentTopicNormalizationTest.php`

**Step 1: Write the failing test**

Create a test proving assignments can resolve a normalized `topic_id` and still render correctly.

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/AssignmentTopicNormalizationTest.php`

Expected: FAIL because assignments still depend on the legacy string topic field.

**Step 3: Write minimal implementation**

- Add nullable `topic_id` to `assignments`
- Backfill from existing `assignments.topic` into `topics`
- Update assignment create/edit flows to use `topic_id`
- Keep legacy reads temporarily for transition safety

**Step 4: Run focused tests**

Run: `php artisan test tests/Feature/AssignmentTopicNormalizationTest.php`

Expected: PASS.

**Step 5: Commit**

```bash
git add database/migrations/2026_03_13_000001_add_topic_id_to_assignments.php app/Models/Assignment.php app/Livewire/Assignment/Create.php app/Livewire/Assignment/Show.php tests/Feature/AssignmentTopicNormalizationTest.php
git commit -m "refactor: normalize assignment topics"
```

### Task 3: Create the unified content root

**Files:**
- Create: `database/migrations/2026_03_13_000002_create_classroom_contents_table.php`
- Create: `app/Models/ClassroomContent.php`
- Modify: `app/Models/Topic.php`
- Modify: `app/Models/Classroom.php`
- Test: `tests/Feature/ClassroomContentModelTest.php`

**Step 1: Write the failing test**

Define expectations for `ClassroomContent` relations, slug routing, type helpers, and submission requirement rules.

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/ClassroomContentModelTest.php`

Expected: FAIL because the model and table do not exist.

**Step 3: Write minimal implementation**

- Create `classroom_contents` with common fields only
- Add Eloquent relationships for classroom, user, topic, attachments, comments, submissions, attendance session, and assignment settings
- Add `type` helper methods and `requiresSubmission()`

**Step 4: Run focused test**

Run: `php artisan test tests/Feature/ClassroomContentModelTest.php`

Expected: PASS.

**Step 5: Commit**

```bash
git add database/migrations/2026_03_13_000002_create_classroom_contents_table.php app/Models/ClassroomContent.php app/Models/Topic.php app/Models/Classroom.php tests/Feature/ClassroomContentModelTest.php
git commit -m "feat: add classroom content root model"
```

### Task 4: Add compatibility links from legacy roots to the new content root

**Files:**
- Create: `database/migrations/2026_03_13_000003_link_legacy_content_tables.php`
- Modify: `app/Models/Assignment.php`
- Modify: `app/Models/Material.php`
- Modify: `app/Models/Announcement.php`
- Test: `tests/Feature/LegacyContentBridgeTest.php`

**Step 1: Write the failing test**

Describe that each legacy root can resolve its linked `ClassroomContent` row.

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/LegacyContentBridgeTest.php`

Expected: FAIL because `content_id` bridge columns and relations do not exist.

**Step 3: Write minimal implementation**

- Add nullable `content_id` to `assignments`, `materials`, and `announcements`
- Add `belongsTo(ClassroomContent::class, 'content_id')`
- Backfill `classroom_contents` from existing rows
- Keep current routes and screens unchanged at this step

**Step 4: Run focused test**

Run: `php artisan test tests/Feature/LegacyContentBridgeTest.php`

Expected: PASS.

**Step 5: Commit**

```bash
git add database/migrations/2026_03_13_000003_link_legacy_content_tables.php app/Models/Assignment.php app/Models/Material.php app/Models/Announcement.php tests/Feature/LegacyContentBridgeTest.php
git commit -m "refactor: link legacy content to classroom content"
```

### Task 5: Move submissions and attendance sessions onto the content root

**Files:**
- Create: `database/migrations/2026_03_13_000004_move_submissions_and_attendance_to_content.php`
- Modify: `app/Models/Submission.php`
- Modify: `app/Models/AttendanceSession.php`
- Modify: `app/Livewire/Assignment/Attendance.php`
- Test: `tests/Feature/ContentOutcomeFlowTest.php`

**Step 1: Write the failing test**

Describe that assignment submission and attendance check-in both resolve through `ClassroomContent` without losing classroom scoping.

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/ContentOutcomeFlowTest.php`

Expected: FAIL because `submissions` and `attendance_sessions` still point at `assignment_id` only.

**Step 3: Write minimal implementation**

- Add `content_id` to `submissions` and `attendance_sessions`
- Backfill from assignment-linked content rows
- Update relationships and attendance check-in flow to read through `ClassroomContent`
- Keep temporary fallback reads where needed until all screens migrate

**Step 4: Run focused tests**

Run: `php artisan test tests/Feature/ContentOutcomeFlowTest.php`

Expected: PASS.

**Step 5: Commit**

```bash
git add database/migrations/2026_03_13_000004_move_submissions_and_attendance_to_content.php app/Models/Submission.php app/Models/AttendanceSession.php app/Livewire/Assignment/Attendance.php tests/Feature/ContentOutcomeFlowTest.php
git commit -m "refactor: move submissions and attendance to content root"
```

### Task 6: Introduce unified stream reads

**Files:**
- Modify: `app/Livewire/Classroom/Show.php`
- Modify: `resources/views/livewire/classroom/show.blade.php`
- Modify: `app/Models/ClassroomContent.php`
- Test: `tests/Feature/ClassroomStreamContentTest.php`

**Step 1: Write the failing test**

Create a test proving the classroom stream can render mixed content types from `classroom_contents` ordered correctly.

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/ClassroomStreamContentTest.php`

Expected: FAIL because the stream still reads split sources.

**Step 3: Write minimal implementation**

- Replace multi-source stream loading with `ClassroomContent` query
- Preserve current ordering, topic usage, and access constraints
- Keep existing Blade partials where possible to avoid a large UI rewrite

**Step 4: Run focused test**

Run: `php artisan test tests/Feature/ClassroomStreamContentTest.php`

Expected: PASS.

**Step 5: Commit**

```bash
git add app/Livewire/Classroom/Show.php resources/views/livewire/classroom/show.blade.php app/Models/ClassroomContent.php tests/Feature/ClassroomStreamContentTest.php
git commit -m "feat: read classroom stream from unified content"
```

### Task 7: Introduce unified content routes and keep legacy redirects

**Files:**
- Modify: `routes/web.php`
- Create: `app/Livewire/Content/Show.php`
- Create: `resources/views/livewire/content/show.blade.php`
- Modify: `app/Livewire/Assignment/Show.php`
- Modify: `app/Livewire/Material/Show.php`
- Test: `tests/Feature/UnifiedContentRouteTest.php`

**Step 1: Write the failing test**

Describe that the app serves a content detail page from a unified route and preserves old assignment/material links via redirect.

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/UnifiedContentRouteTest.php`

Expected: FAIL because no unified content route exists.

**Step 3: Write minimal implementation**

- Add `/c/{classroom}/content/{content}` route
- Create a thin `Content\Show` controller/component that delegates by type
- Redirect legacy assignment/material detail routes to the content route

**Step 4: Run focused test**

Run: `php artisan test tests/Feature/UnifiedContentRouteTest.php`

Expected: PASS.

**Step 5: Commit**

```bash
git add routes/web.php app/Livewire/Content/Show.php resources/views/livewire/content/show.blade.php app/Livewire/Assignment/Show.php app/Livewire/Material/Show.php tests/Feature/UnifiedContentRouteTest.php
git commit -m "feat: add unified classroom content routes"
```

### Task 8: Remove legacy writes and finalize constraints

**Files:**
- Create: `database/migrations/2026_03_13_000005_finalize_classroom_content_unification.php`
- Modify: `app/Livewire/Assignment/Create.php`
- Modify: `app/Livewire/Material/Create.php`
- Modify: `app/Livewire/Announcement/Create.php`
- Test: `tests/Feature/ClassroomContentWritePathTest.php`

**Step 1: Write the failing test**

Describe that new content creation writes through `ClassroomContent` and creates only the required detail rows.

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/ClassroomContentWritePathTest.php`

Expected: FAIL because write paths still start from legacy roots.

**Step 3: Write minimal implementation**

- Update create flows to write `ClassroomContent` first
- Create detail rows only when required by type
- Tighten database constraints now that reads/writes are migrated
- Remove temporary dual-write logic

**Step 4: Run focused test**

Run: `php artisan test tests/Feature/ClassroomContentWritePathTest.php`

Expected: PASS.

**Step 5: Commit**

```bash
git add database/migrations/2026_03_13_000005_finalize_classroom_content_unification.php app/Livewire/Assignment/Create.php app/Livewire/Material/Create.php app/Livewire/Announcement/Create.php tests/Feature/ClassroomContentWritePathTest.php
git commit -m "refactor: finalize classroom content write path"
```

### Task 9: Verification and cleanup

**Files:**
- Modify: `docs/plans/2026-03-13-classroom-content-unification-design.md`
- Modify: `docs/plans/2026-03-13-classroom-content-unification-plan.md`

**Step 1: Run targeted tests for new flows**

Run:

```bash
php artisan test tests/Feature/ClassroomContentMigrationSafetyTest.php
php artisan test tests/Feature/AssignmentTopicNormalizationTest.php
php artisan test tests/Feature/ClassroomContentModelTest.php
php artisan test tests/Feature/LegacyContentBridgeTest.php
php artisan test tests/Feature/ContentOutcomeFlowTest.php
php artisan test tests/Feature/ClassroomStreamContentTest.php
php artisan test tests/Feature/UnifiedContentRouteTest.php
php artisan test tests/Feature/ClassroomContentWritePathTest.php
```

Expected: PASS.

**Step 2: Run full verification**

Run:

```bash
php artisan test
./vendor/bin/pint --test
```

Expected: exit code `0` for both.

**Step 3: Update plan docs with any migration learnings**

Record any deviations, removed transitional code, or follow-up work.

**Step 4: Commit**

```bash
git add docs/plans/2026-03-13-classroom-content-unification-design.md docs/plans/2026-03-13-classroom-content-unification-plan.md
git commit -m "docs: record classroom content unification rollout"
```

---

Plan complete and saved to `docs/plans/2026-03-13-classroom-content-unification-plan.md`. Two execution options:

**1. Subagent-Driven (this session)** - I dispatch fresh subagent per task, review between tasks, fast iteration

**2. Parallel Session (separate)** - Open new session with executing-plans, batch execution with checkpoints

Which approach?
