# Classroom Content Unification Design

**Date**: 2026-03-13
**Status**: Proposed
**Goal**: Simplify LiteLearning's schema by introducing one classroom content root for assignments, materials, announcements, and attendance while keeping per-student outcomes and attendance runtime state in specialized tables.

---

## Problem

The current schema splits top-level classroom content across multiple roots:

- `assignments` stores graded work, attendance-backed activities, and several content-like `type` values.
- `materials` stores teacher-authored learning materials with a normalized `topic_id`.
- `announcements` is its own root table.
- `submissions` already acts as the shared per-student result table.
- `attendance_sessions` already acts as attendance-only runtime state.

This causes several inconsistencies:

- Duplicate top-level content concepts across tables.
- `assignments.topic` is a string, while `materials.topic_id` is normalized.
- Stream/feed and routing are split by content type.
- The code already wants a unified content concept, but the schema is only partially unified.

---

## Recommendation

Use a hybrid schema:

1. Add a new `classroom_contents` table as the single root for classroom content metadata.
2. Keep specialized child tables for type-specific settings and state.
3. Keep `submissions` separate as the per-student result table.
4. Keep `attendance_sessions` separate as transient attendance session state.

This keeps the schema clean without forcing unrelated lifecycles into one table.

---

## Proposed Schema

### Root Table: `classroom_contents`

Common fields shared by all classroom content:

- `id`
- `classroom_id`
- `user_id`
- `type` (`announcement`, `material`, `assignment`, `attendance`)
- `title`
- `body`
- `slug`
- `topic_id` nullable
- `status` (`draft`, `published`, `closed`, `archived`)
- `published_at` nullable
- `due_at` nullable
- `sort_order` nullable
- timestamps

### Child Tables

#### `assignment_settings`

Assignment/attendance grading and submission behavior:

- `content_id`
- `max_score`
- `allow_late_submission`
- `submission_mode`
- `reward_coins`
- `reward_xp`

#### `submissions`

Keep existing purpose, but point to content root:

- `content_id`
- `user_id`
- `content`
- `status`
- `score`
- `feedback`
- `turned_in_at`
- `graded_at`

#### `attendance_sessions`

Keep attendance runtime/session state, but point to content root:

- `content_id`
- `current_code`
- `is_active`
- `started_at`
- `code_rotated_at`

#### Optional detail tables later

Only add these if needed by future complexity:

- `material_settings`
- `announcement_settings`

Do not create them up front unless real requirements appear.

---

## Domain Rules

- `assignment` and `attendance` may have rows in `assignment_settings`.
- Only `attendance` may have a row in `attendance_sessions`.
- Only content types that require per-student outcomes may have rows in `submissions`.
- `material` and `announcement` should not create `submissions`.
- All top-level stream items should come from `classroom_contents`.

---

## Model Direction

### New root model

- Create `app/Models/ClassroomContent.php` as the canonical content root.

Core relationships:

- `classroom()`
- `user()`
- `topic()`
- `attachments()`
- `comments()`
- `assignmentSettings()`
- `attendanceSession()`
- `submissions()`

Convenience helpers:

- `isAssignment()`
- `isAttendance()`
- `isMaterial()`
- `isAnnouncement()`
- `requiresSubmission()`

### Legacy models during transition

- Keep `Assignment`, `Material`, and `Announcement` temporarily during migration.
- Convert them into compatibility layers or adapters while screens move to `ClassroomContent`.
- Remove them only after routes, tests, and data migration are stable.

---

## Routing Direction

Current routes are split:

- `/c/{classroom}/a/{assignment}`
- `/c/{classroom}/m/{material}`

Target direction:

- Add a single content route, e.g. `/c/{classroom}/work/{content}` or `/c/{classroom}/content/{content}`.
- Keep old routes temporarily and redirect them to the content route during migration.

This reduces route sprawl and supports a unified stream/detail experience.

---

## Why Not a Single Mega-Table

Do not collapse everything into one table with dozens of nullable columns.

Why:

- `submissions` is one-row-per-student, not one-row-per-content.
- `attendance_sessions` has short-lived runtime state and code rotation behavior.
- Materials and announcements do not share the same grading or submission rules.
- A mega-table creates sparse columns, weak constraints, and fragile branching logic.

---

## Migration Strategy

### Phase 1: Normalize current inconsistencies

- Add `topic_id` support to assignments.
- Backfill assignment topics into `topics`.
- Stop writing to `assignments.topic` once `topic_id` is ready.

### Phase 2: Introduce root content table

- Create `classroom_contents`.
- Backfill rows from `assignments`, `materials`, and `announcements`.
- Add `content_id` to `submissions` and `attendance_sessions`.
- Keep existing foreign keys temporarily for dual-read/dual-write migration safety.

### Phase 3: Move application reads

- Update stream queries to read from `ClassroomContent`.
- Update show/create/edit flows incrementally.
- Introduce a unified content route and component structure.

### Phase 4: Remove legacy ownership

- Stop writing to legacy root tables.
- Remove old foreign keys after verification.
- Delete deprecated tables or keep them as historical views only if needed.

---

## Testing Requirements

Add or update Feature tests for:

- Unified classroom stream rendering mixed content types.
- Access control for nested classroom/content relationships.
- Assignment submission flow through `ClassroomContent`.
- Attendance check-in flow through `ClassroomContent` + `AttendanceSession`.
- Material and announcement visibility with no submission records.
- Legacy route redirects during migration.

---

## Recommendation Summary

The cleanest path is:

- unify top-level classroom content,
- keep per-student results separate,
- keep attendance runtime state separate,
- migrate in phases instead of rewriting everything at once.

This gives a simpler schema, simpler stream queries, and lower migration risk.
