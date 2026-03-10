# Implementation Plan — Trait Refactoring

**Design**: `docs/plans/2026-03-10-trait-refactoring-design.md`
**Date**: 2026-03-10

---

## Phase 1: Foundation (Migration + Model Traits)

### Task 1.1: Create migration — add slug to announcements + attendance_sessions
- Create migration file
- Add nullable slug column to both tables
- Backfill existing rows with generated 16-char slugs
- Make slug non-nullable + unique
- Verify: `php artisan migrate` succeeds

### Task 1.2: Create `HasSlug` trait
- File: `app/Models/Traits/HasSlug.php`
- Implement: `getRouteKeyName()`, `bootHasSlug()`, `generateUniqueSlug()`
- Use `static::where()` for per-model table scope
- Verify: unit test or manual — trait can be used without error

### Task 1.3: Create `BelongsToClassroom` trait
- File: `app/Models/Traits/BelongsToClassroom.php`
- Implement: `classroom(): BelongsTo`, `user(): BelongsTo`
- Verify: trait compiles without error

### Task 1.4: Create `HasCommentsAndAttachments` trait
- File: `app/Models/Traits/HasCommentsAndAttachments.php`
- Implement: `comments(): MorphMany`, `attachments(): MorphMany`
- Verify: trait compiles without error

## Phase 2: Model Refactoring

### Task 2.1: Refactor Assignment model
- Replace inline slug logic with `use HasSlug`
- Replace classroom/user relationships with `use BelongsToClassroom`
- Replace comments/attachments with `use HasCommentsAndAttachments`
- Keep all Assignment-only logic (submissions, types, due-date, etc.)
- Verify: `php artisan test` — all 27 pass

### Task 2.2: Refactor Material model
- Replace inline slug logic with `use HasSlug`
- Replace classroom/user relationships with `use BelongsToClassroom`
- Replace comments/attachments with `use HasCommentsAndAttachments`
- Verify: `php artisan test` — all 27 pass

### Task 2.3: Refactor Announcement model
- Add `use HasSlug` (new capability) + add `slug` to `$fillable`
- Replace classroom/user relationships with `use BelongsToClassroom`
- Replace comments/attachments with `use HasCommentsAndAttachments`
- Verify: `php artisan test` — all 27 pass

### Task 2.4: Refactor AttendanceSession model
- Add `use HasSlug` (new capability) + add `slug` to `$fillable`
- Keep assignment() relationship and session methods
- Verify: `php artisan test` — all 27 pass

## Phase 3: Livewire Concerns

### Task 3.1: Create `VerifiesContentAccess` concern
- File: `app/Livewire/Concerns/VerifiesContentAccess.php`
- Implement: `verifyContentAccess(Classroom, Model, string $fk)`

### Task 3.2: Create `HasEditableContent` concern
- File: `app/Livewire/Concerns/HasEditableContent.php`
- Abstract: `editableFields(): array`
- Implement: `openEditTab()`, `cancelEditTab()`, `syncEditFields()`, delete modal methods

### Task 3.3: Create `HasFileUpload` concern
- File: `app/Livewire/Concerns/HasFileUpload.php`
- Abstract: `allowedMimes(): array`, `maxFileSizeKb(): int`
- Implement: `updatedFile()`, `removeFile()`, `generateAttachmentId()`

### Task 3.4: Create `HasTopicSelector` concern
- File: `app/Livewire/Concerns/HasTopicSelector.php`
- Implement: `getTopicsProperty()`, `resolveOrCreateTopic()`

## Phase 4: Livewire Component Refactoring

### Task 4.1: Refactor Assignment\Show
- Use VerifiesContentAccess, HasEditableContent, HasTopicSelector
- Implement `editableFields()` returning Assignment-specific fields
- Keep student submission logic inline
- Verify: `php artisan test` — all pass

### Task 4.2: Refactor Material\Show
- Use VerifiesContentAccess, HasEditableContent, HasTopicSelector
- Add `#[Locked]` to `$material` property (bug fix)
- Implement `editableFields()` returning Material-specific fields
- Verify: `php artisan test` — all pass

### Task 4.3: Refactor Assignment\Create
- Use HasFileUpload, HasTopicSelector
- Implement `allowedMimes()`, `maxFileSizeKb()`
- Verify: `php artisan test` — all pass

### Task 4.4: Refactor Material\Create
- Use HasFileUpload, HasTopicSelector
- Implement `allowedMimes()`, `maxFileSizeKb()`
- Verify: `php artisan test` — all pass

### Task 4.5: Refactor Assignment\Attendance
- Use VerifiesContentAccess (fixes missing IDOR bug)
- Call `verifyContentAccess()` in mount()
- Verify: `php artisan test` — all pass

## Phase 5: Verification

### Task 5.1: Full test suite
- `php artisan test` — 27/27 pass
- `./vendor/bin/pint --test` — code style clean

---

## Execution Strategy: Subagent-Driven

- Phase 1 tasks 1.2-1.4 (3 traits) → parallel subagents
- Phase 2 tasks → parallel after Phase 1 (4 model refactors independent)
- Phase 3 tasks 3.1-3.4 (4 concerns) → parallel subagents
- Phase 4 tasks → parallel after Phase 3 (5 component refactors independent)
- Phase 5 → sequential final verification
