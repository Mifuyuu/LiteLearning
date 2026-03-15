# Scheduled Publish — Design

**Date:** 2026-03-15  
**Feature:** Allow teachers to create classwork in advance and have it automatically become published at a specified date/time.  
**Scope:** All classwork types (assignment, material, announcement, attendance).

---

## Requirements

- Teacher can optionally set a future `published_at` datetime when creating classwork.
- When `published_at` is set and in the future, the item is in `scheduled` state.
- At the scheduled time (within ~1 minute), the item flips to `published` automatically.
- For assignments: submissions are created for all enrolled students at publish time.
- Students cannot see scheduled items; teachers see them with a visual indicator.
- Teachers can edit or clear the scheduled time before it fires (reverts to `draft` if cleared).
- No student notifications — status flip only.

---

## Schema Changes

### 1. `classwork_items` — add `published_at`

```php
$table->timestamp('published_at')->nullable()->after('description');
```

Semantics: `NULL` = not scheduled (visible now or governed by assignment status). A non-null value means the item should be hidden from students until that time arrives.

### 2. `assignments.status` — extend enum

Current: `ENUM('draft', 'published', 'closed')`  
New: `ENUM('draft', 'published', 'closed', 'scheduled')`

Used only for assignments. Non-assignment types use `classwork_items.published_at` for scheduling.

---

## Visibility Rules

**Students** see a classwork item only when:
- For assignments: `assignments.status IN ('published', 'closed')`
- For non-assignment types: `classwork_items.published_at IS NULL OR classwork_items.published_at <= NOW()`

**Teachers** see all items, with a clock badge on items where `published_at > now()` or `status = 'scheduled'`.

---

## Scheduler Command

**File:** `app/Console/Commands/PublishScheduledClasswork.php`  
**Signature:** `classwork:publish-scheduled`  
**Registered in:** `routes/console.php` → `Schedule::command('classwork:publish-scheduled')->everyMinute()`

### Logic

1. **Assignments:** Find all assignments where `status = 'scheduled'` and `classwork_items.published_at <= now()`:
   - Set `assignments.status = 'published'`
   - Create `Submission` records (`status = 'assigned'`) for every enrolled student in the classroom (skip if submission already exists).
2. **Non-assignment types:** No extra action needed — the visibility rule gates them by `published_at` automatically. (Optionally: clear `published_at` after it fires to avoid re-querying, but not required.)

The command runs in a single DB transaction per assignment to keep create-submissions atomic.

---

## Create Form Changes (`app/Livewire/Assignment/Create.php`)

### New property

```php
public ?string $published_at = null;
```

### Validation addition

```php
'published_at' => 'nullable|date|after:now',
```

### Status override logic

After validation, before save:
```php
if ($this->published_at && now()->lt($this->published_at)) {
    $this->status = 'scheduled';
}
```

For assignments: `published_at` is passed to `classwork_item.published_at`.  
For non-assignment types: same — stored on `classwork_item.published_at`, status toggle is hidden.

### Submission creation guard

Existing code: create submissions only when `status = 'published'`. This remains — scheduled items do NOT create submissions at save time; the scheduler handles it.

---

## View Changes

### `resources/views/livewire/assignment/create.blade.php`

Add a `datetime-local` input below `due_date`:

```html
<!-- Publish At (optional) -->
<input type="datetime-local" wire:model="published_at" ... />
```

When filled with a future time, the draft/published toggle is hidden or disabled (the status will be `scheduled`).

### Classroom stream (`resources/views/livewire/classroom/show.blade.php`)

- Filter student view: exclude items where `status = 'scheduled'` or `published_at > now()`.
- Teacher view: show all items; render a clock badge on scheduled items.

---

## Testing

- Unit: `PublishScheduledClasswork` command — assert assignments flip to published, submissions created, non-assignment items become visible.
- Feature: Teacher creates scheduled assignment → students cannot see it → after time advances → students can see it and have submissions.
- Edge: `published_at` in the past at save time → treat as `published` immediately (validation `after:now` prevents this for new creates, but edit flow may allow it).

---

## Out of Scope

- Editing scheduled time after creation (separate ticket; model supports it, UI not built).
- Student notifications on publish.
- Bulk schedule via CSV or repeat/recurring schedules.
