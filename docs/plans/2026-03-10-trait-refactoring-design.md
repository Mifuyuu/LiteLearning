# Trait Refactoring Design — Shared Content Logic

**Date**: 2026-03-10
**Status**: Approved
**Goal**: Extract duplicated code from Assignment, Material, Announcement, and AttendanceSession into shared Traits, following Laravel conventions (like `SoftDeletes`, `HasFactory`).

---

## Problem

Assignment, Material, and Announcement models duplicate identical logic:
- `generateUniqueSlug()` — 14 identical lines
- `getRouteKeyName()` — identical
- `booted()` auto-slug on creating — identical
- `classroom(): BelongsTo` — identical
- `user(): BelongsTo` — identical
- `comments(): MorphMany` — identical
- `attachments(): MorphMany` — identical

Livewire components duplicate:
- mount() IDOR check pattern — 4 components
- Edit tab trio (open/cancel/sync) — 2 components
- Delete modal trio — 2 components
- `getTopicsProperty` computed — 4 components
- Topic `firstOrCreate` — 4 places
- File upload (updatedFile/removeFile) — 2 components

Additionally:
- Announcement and AttendanceSession lack `slug` column (to be added)
- Material\Show has `$material` property without `#[Locked]` (bug)
- Assignment\Attendance has no IDOR check in mount() (bug)

---

## Approach: Pure Traits

### Why Traits over Abstract Base Class

1. Only 3-4 content models — doesn't justify inheritance hierarchy
2. Each model shares ~40% logic, not 80%+ (Assignment has massive unique logic)
3. Laravel convention is Traits (`SoftDeletes`, `HasFactory`, `Notifiable`)
4. Flexible composition — each model picks only what it needs
5. No inheritance chain change — all models stay `extends Model`

---

## Model Traits (`app/Models/Traits/`)

### 1. `HasSlug`
- `getRouteKeyName(): string` → returns `'slug'`
- `bootHasSlug(): void` → `static::creating()` auto-generate slug
- `generateUniqueSlug(): string` → 16-char alphanumeric, retry if exists
- Uses `static::where('slug', ...)` so each model checks its own table

**Used by**: Assignment, Material, Announcement, AttendanceSession

### 2. `BelongsToClassroom`
- `classroom(): BelongsTo` → `$this->belongsTo(Classroom::class)`
- `user(): BelongsTo` → `$this->belongsTo(User::class)`

**Used by**: Assignment, Material, Announcement
**Not used by**: AttendanceSession (links through assignment, not directly)

### 3. `HasCommentsAndAttachments`
- `comments(): MorphMany` → `$this->morphMany(Comment::class, 'commentable')->latest()`
- `attachments(): MorphMany` → `$this->morphMany(Attachment::class, 'attachable')`

**Used by**: Assignment, Material, Announcement
**Not used by**: AttendanceSession

---

## Livewire Concerns (`app/Livewire/Concerns/`)

### 4. `VerifiesContentAccess`
```php
protected function verifyContentAccess(Classroom $classroom, Model $content, string $fkColumn = 'classroom_id'): void
{
    abort_unless($content->{$fkColumn} === $classroom->id, 404);
    abort_unless($classroom->hasAccess(auth()->user()), 403);
}
```
**Used by**: Assignment\Show, Material\Show, Assignment\Attendance

### 5. `HasEditableContent`
- Abstract: `editableFields(): array` — each component defines which fields to sync
- `openEditTab(): void` — sync fields from model → edit properties
- `cancelEditTab(): void` — reset edit properties
- `syncEditFields(): void` — copy model values to edit* properties
- `openDeleteModal() / closeDeleteModal()` — toggle `$showDeleteModal`

**Used by**: Assignment\Show, Material\Show

### 6. `HasFileUpload`
- Abstract: `allowedMimes(): array`
- Abstract: `maxFileSizeKb(): int`
- `updatedFile(): void` — validate + add to `$uploadedFiles[]`
- `removeFile(int $index): void` — remove from array
- `generateAttachmentId(): string` — 8-char random

**Used by**: Assignment\Create, Material\Create

### 7. `HasTopicSelector`
- `getTopicsProperty(): Collection` — `Topic::where('classroom_id', ...)->get()`
- `resolveOrCreateTopic(string $name, int $classroomId): int` — `firstOrCreate`, returns `topic_id`

**Used by**: Assignment\Show, Material\Show, Assignment\Create, Material\Create

---

## Migration

**New migration**: `2026_03_10_000001_add_slug_to_announcements_and_attendance_sessions.php`

- Add `slug` (string, unique, nullable temporarily) to `announcements`
- Add `slug` (string, unique, nullable temporarily) to `attendance_sessions`
- Backfill existing rows with generated slugs
- Make columns non-nullable after backfill

---

## Trait → Model Matrix

| Model             | HasSlug | BelongsToClassroom | HasCommentsAndAttachments |
|-------------------|---------|--------------------|----|
| Assignment        | ✅      | ✅                 | ✅ |
| Material          | ✅      | ✅                 | ✅ |
| Announcement      | ✅      | ✅                 | ✅ |
| AttendanceSession | ✅      | ❌                 | ❌ |

## Concern → Livewire Matrix

| Component              | VerifiesContentAccess | HasEditableContent | HasFileUpload | HasTopicSelector |
|------------------------|---|---|---|---|
| Assignment\Show        | ✅ | ✅ | ❌ | ✅ |
| Material\Show          | ✅ | ✅ | ❌ | ✅ |
| Assignment\Create      | ❌ | ❌ | ✅ | ✅ |
| Material\Create        | ❌ | ❌ | ✅ | ✅ |
| Assignment\Attendance  | ✅ | ❌ | ❌ | ❌ |

---

## Bug Fixes (during refactoring)

1. **Material\Show**: Add `#[Locked]` to `$material` property
2. **Assignment\Attendance**: Add IDOR check via `VerifiesContentAccess` trait

---

## What Does NOT Change

- Database tables stay separate (assignments, materials, announcements, attendance_sessions)
- Blade templates — no changes
- Routes — no changes
- Assignment-only logic stays in Assignment model (submissions, due-date, grading, type helpers)
- AttendanceSession session logic stays in AttendanceSession (start/stop/rotateCode)
- Student submission upload in Assignment\Show stays inline (different from Create upload)

---

## Verification

- All 27 existing tests must pass after refactoring
- `./vendor/bin/pint --test` must pass
- `php artisan test` exit code 0
