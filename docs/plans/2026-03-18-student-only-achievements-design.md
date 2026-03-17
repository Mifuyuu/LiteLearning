# Student-Only Achievements Design

**Date:** 2026-03-18

## Goal

Make the achievements system consistently student-only by removing teacher-focused achievements from seeded data and deleting dead teacher unlock logic, while keeping the existing student achievements page and admin management UI intact.

## Current State

- `app/Services/GamificationService.php` already gates rewards and achievement unlocks behind `isEligible()`, which only returns `true` for students.
- `database/seeders/GamificationFeaturesSeeder.php` still seeds teacher-oriented achievements such as `first_classroom_created`, `classroom_builder`, and `first_assignment_created`.
- `app/Livewire/Student/Achievements.php` and `routes/web.php` already expose achievements only to students.
- `app/Livewire/Admin/Achievements.php` is a generic management UI for achievement records and can remain in place as the content-management surface.

## Decision

Use the smallest consistent change set:

1. Remove teacher-only achievement codes from the seeder.
2. Remove dead teacher achievement unlock branches from `GamificationService`.
3. Keep the student achievements route and page unchanged.
4. Keep the admin CRUD UI unchanged so admins can continue managing the remaining student achievements.
5. Add tests proving students can still unlock student achievements and teacher-triggered achievement branches are gone from the supported system behavior.

## Alternatives Considered

### 1. Hard-remove teacher achievements everywhere (chosen)

- Pros: seeded data matches runtime behavior; no stale concepts; smallest long-term maintenance burden.
- Cons: removes teacher-oriented records from existing reseeded databases.

### 2. Keep teacher achievements but mark them inactive

- Pros: lower visible data churn.
- Cons: keeps dead concepts in the system and does not satisfy the request cleanly.

### 3. Add role targeting to achievements

- Pros: more flexible in the future.
- Cons: requires schema, admin UI, validation, and service changes that are unnecessary for the requested scope.

## Scope

### In Scope

- `database/seeders/GamificationFeaturesSeeder.php`
- `app/Services/GamificationService.php`
- `tests/Feature/GamificationTest.php`

### Out of Scope

- New database columns or migrations
- Changes to student achievements page layout
- Changes to admin achievements CRUD flow beyond the data it manages

## Testing Strategy

- Add/adjust feature tests in `tests/Feature/GamificationTest.php`.
- Verify a student still unlocks supported achievements.
- Verify non-student reward guards still prevent teachers from receiving coins/achievements.
- Verify reseeding removes stale teacher-only achievement codes and leaves only student-oriented achievements.

## Expected Outcome

After reseeding, the database contains only student achievements, the service no longer carries teacher achievement unlock branches, and the runtime behavior remains aligned with the existing student-only gamification model.
