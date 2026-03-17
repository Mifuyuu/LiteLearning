# Student Classroom Filter Removal Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Remove the student-only classroom filter pills so students always see their active enrolled classrooms on the classrooms page.

**Architecture:** Keep the teacher flow unchanged, including the archived toggle. Simplify the student branch in the Livewire component to always return non-archived enrolled classrooms, and remove the student filter controls from the Blade view. Cover the behavior with a focused regression test before editing production code.

**Tech Stack:** Laravel 12, Livewire v4, Blade, PHPUnit, SQLite in-memory

---

## Task 1: Regression Test for Student Classroom Index

**Files:**
- Create: `tests/Feature/ClassroomIndexTest.php`
- Modify: `app/Livewire/Classroom/Index.php`
- Modify: `resources/views/livewire/classroom/index.blade.php`

**Step 1: Write the failing test**

Create `tests/Feature/ClassroomIndexTest.php` with a student-facing page test that:
- creates a student user
- enrolls the student in one active classroom and one archived classroom
- requests `route('classrooms')`
- asserts the active classroom is visible
- asserts the archived classroom is hidden
- asserts the student filter labels are not rendered (`ทั้งหมด`, `ลงทะเบียน`, `เก็บถาวร`)

**Step 2: Run test to verify it fails**

```bash
php artisan test tests/Feature/ClassroomIndexTest.php
```

Expected: FAIL because the current student page still renders the filter pills and can still expose archived-only state via the filter logic.

**Step 3: Write minimal implementation**

In `app/Livewire/Classroom/Index.php`:
- remove the student `filter` state
- remove the archived/all branching for students
- always query `$user->enrolledClassrooms()->where('is_archived', false)` for the student path

In `resources/views/livewire/classroom/index.blade.php`:
- remove the student filter pills block
- keep the search input and count
- keep the teacher archived checkbox unchanged

**Step 4: Run test to verify it passes**

```bash
php artisan test tests/Feature/ClassroomIndexTest.php
```

Expected: PASS

**Step 5: Run focused verification**

```bash
php artisan test tests/Feature/ClassroomIndexTest.php tests/Feature/SecurityTest.php
```

Expected: PASS

**Step 6: Commit**

```bash
git add tests/Feature/ClassroomIndexTest.php app/Livewire/Classroom/Index.php resources/views/livewire/classroom/index.blade.php docs/plans/2026-03-18-student-classroom-filter-removal.md
git commit -m "refactor: remove student classroom filters"
```
