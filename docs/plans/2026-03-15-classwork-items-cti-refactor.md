# ClassworkItems CTI Refactor Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Extract shared fields (`classroom_id`, `user_id`, `topic_id`, `title`, `slug`, `description`) from `assignments` and `materials` into a new `classwork_items` table using Class Table Inheritance, and fix `assignments.topic` (varchar) to use `classwork_items.topic_id` (FK).

**Architecture:** New `classwork_items` table holds all shared identity/metadata. `assignments` and `materials` each gain a `classwork_item_id` FK (unique, 1:1) and retain only their type-specific columns. PHP-side: `ClassworkItem` model with `HasOne` to each child. Child models gain proxy accessors for shared fields so all existing code reading `$assignment->title` etc. continues working. All queries that previously used `assignments.classroom_id` are rewritten to join/use `classwork_items`.

**Tech Stack:** Laravel 12, MySQL, Livewire v4, Eloquent ORM

---

## Key Constraints

- **Preserve all existing behavior** — `$assignment->title`, `$assignment->classroom_id`, `$assignment->slug`, `$assignment->topic`, `$assignment->user_id` must all still work as getters (via proxy accessors or `HasSlug`/`BelongsToClassroom` delegation)
- **Slug must remain globally unique** across `classwork_items` (not per-table)
- **`HasSlug` trait** currently does `static::where('slug', $slug)->exists()` — after CTI, slug lives on `classwork_items`, so this must look in that table
- **`VerifiesContentAccess` trait** currently checks `$content->classroom_id === $classroom->id` — after CTI, classroom_id is on the `ClassworkItem`, not directly on Assignment/Material. Needs to access it through the relationship.
- **`BelongsToClassroom` trait** provides `classroom()` and `user()` BelongsTo on Assignment/Material. After CTI, these relationships move to `ClassworkItem`. We keep them on Assignment/Material as delegation through `classworkItem`.
- **`Topic::assignments()`** currently does `Assignment::where('classroom_id', X)->where('topic', name)` — both columns being moved. Must rewrite using `classwork_items` join.
- **`GradeReport`** does `->where('topic', $filterTopic)` and `->pluck('topic')` directly on Assignment query — must join classwork_items.
- **`Dashboard`** does `Assignment::whereIn('classroom_id', ...)` — must join classwork_items.
- **`show.blade.php`** does `$assignments->groupBy(fn($a) => $a->topic ?? '__none__')` — proxy accessor handles this.
- **Data migration** must run BEFORE structural migrations that drop columns — or do it in a single migration with careful ordering.
- **No test files need updating** (tests use factories; factories will need updating).

---

## Migration Strategy (Ordering Matters)

1. Create `classwork_items` table (no FKs to assignments/materials yet)
2. Add `classwork_item_id` nullable to `assignments` and `materials`
3. Data migration: INSERT into classwork_items from assignments rows, then materials rows, then UPDATE the FK columns
4. Add NOT NULL + UNIQUE constraints + drop old shared columns from assignments and materials

This avoids needing multiple migration files and keeps the data-safe ordering intact in a single transaction where possible.

---

## Task 1: Migration — Create `classwork_items`, backfill data, alter `assignments` & `materials`

**Files:**
- Create: `database/migrations/XXXX_XX_XX_create_classwork_items_table.php`

**Step 1: Create the migration file**

```bash
php artisan make:migration create_classwork_items_table
```

**Step 2: Write the migration**

The migration does everything in order:
1. Create `classwork_items`
2. Add nullable `classwork_item_id` to `assignments` and `materials`
3. Backfill data (INSERT + UPDATE)
4. Make `classwork_item_id` NOT NULL + UNIQUE + add FK constraints
5. Drop shared columns from `assignments` and `materials`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create classwork_items table
        Schema::create('classwork_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['assignment', 'material']);
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 255);
            $table->string('slug', 32)->unique();
            $table->longText('description')->nullable();
            $table->timestamps();
        });

        // 2. Add nullable classwork_item_id to assignments and materials
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
        });

        // 3. Backfill classwork_items from assignments
        DB::statement("
            INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
            SELECT
                'assignment',
                a.classroom_id,
                a.user_id,
                t.id,
                a.title,
                a.slug,
                a.description,
                a.created_at,
                a.updated_at
            FROM assignments a
            LEFT JOIN topics t ON t.classroom_id = a.classroom_id AND t.name = a.topic
        ");

        // Update assignments.classwork_item_id to point to the newly created rows
        DB::statement("
            UPDATE assignments a
            JOIN classwork_items ci ON ci.slug = a.slug AND ci.type = 'assignment'
            SET a.classwork_item_id = ci.id
        ");

        // 4. Backfill classwork_items from materials
        DB::statement("
            INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
            SELECT
                'material',
                m.classroom_id,
                m.user_id,
                m.topic_id,
                m.title,
                m.slug,
                m.description,
                m.created_at,
                m.updated_at
            FROM materials m
        ");

        // Update materials.classwork_item_id
        DB::statement("
            UPDATE materials m
            JOIN classwork_items ci ON ci.slug = m.slug AND ci.type = 'material'
            SET m.classwork_item_id = ci.id
        ");

        // 5. Make classwork_item_id NOT NULL + UNIQUE on both tables
        Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
            $table->unique('classwork_item_id');
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
            $table->unique('classwork_item_id');
        });

        // 6. Drop shared columns from assignments
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'classroom_id', 'title', 'slug', 'description', 'topic']);
        });

        // 7. Drop shared columns from materials
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'classroom_id', 'title', 'slug', 'description', 'topic_id']);
        });
    }

    public function down(): void
    {
        // Reverse: re-add columns, backfill from classwork_items, drop classwork_item_id, drop table
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255)->nullable()->after('classroom_id');
            $table->string('slug', 32)->nullable()->unique()->after('title');
            $table->longText('description')->nullable()->after('slug');
            $table->string('topic', 255)->nullable()->after('description');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255)->nullable()->after('classroom_id');
            $table->string('slug', 32)->nullable()->unique()->after('title');
            $table->longText('description')->nullable()->after('slug');
            $table->foreignId('topic_id')->nullable()->after('description')->constrained()->nullOnDelete();
        });

        DB::statement("
            UPDATE assignments a
            JOIN classwork_items ci ON ci.id = a.classwork_item_id
            SET a.user_id = ci.user_id,
                a.classroom_id = ci.classroom_id,
                a.title = ci.title,
                a.slug = ci.slug,
                a.description = ci.description
        ");

        DB::statement("
            UPDATE assignments a
            JOIN classwork_items ci ON ci.id = a.classwork_item_id
            LEFT JOIN topics t ON t.id = ci.topic_id
            SET a.topic = t.name
        ");

        DB::statement("
            UPDATE materials m
            JOIN classwork_items ci ON ci.id = m.classwork_item_id
            SET m.user_id = ci.user_id,
                m.classroom_id = ci.classroom_id,
                m.title = ci.title,
                m.slug = ci.slug,
                m.description = ci.description,
                m.topic_id = ci.topic_id
        ");

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropUnique(['classwork_item_id']);
            $table->dropConstrainedForeignId('classwork_item_id');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropUnique(['classwork_item_id']);
            $table->dropConstrainedForeignId('classwork_item_id');
        });

        Schema::dropIfExists('classwork_items');
    }
};
```

**Step 3: Run the migration**

```bash
php artisan migrate
```

Expected: `Migrating: XXXX_XX_XX_create_classwork_items_table ... Migrated`

---

## Task 2: `ClassworkItem` Model

**Files:**
- Create: `app/Models/ClassworkItem.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassworkItem extends Model
{
    protected $fillable = [
        'type',
        'classroom_id',
        'user_id',
        'topic_id',
        'title',
        'slug',
        'description',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function material(): HasOne
    {
        return $this->hasOne(Material::class);
    }
}
```

---

## Task 3: Update `Assignment` Model

**Files:**
- Modify: `app/Models/Assignment.php`

**Key changes:**
1. Remove from `$fillable`: `user_id`, `title`, `slug`, `description`, `classroom_id`, `topic`
2. Add to `$fillable`: `classwork_item_id`
3. Remove `BelongsToClassroom` trait (classroom/user relationships move to ClassworkItem)
4. Remove `HasSlug` trait (slug now lives on ClassworkItem; route binding handled differently)
5. Add `classworkItem()` BelongsTo relationship
6. Add proxy accessors for all shared fields: `getClassroomIdAttribute`, `getUserIdAttribute`, `getTitleAttribute`, `getSlugAttribute`, `getDescriptionAttribute`, `getTopicAttribute`, `getClassroomAttribute`, `getUserAttribute`
7. Add `getRouteKeyName()` → `'slug'` and `resolveRouteBinding()` that queries via `classwork_items`
8. Keep all other existing methods unchanged

```php
<?php

namespace App\Models;

use App\Models\Traits\HasCommentsAndAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assignment extends Model
{
    use HasCommentsAndAttachments, HasFactory;

    protected $fillable = [
        'classwork_item_id',
        'exp_reward',
        'coin_reward',
        'due_date',
        'status',
        'type',
        'allow_late_submission',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'due_date' => 'datetime',
            'allow_late_submission' => 'boolean',
            'exp_reward' => 'integer',
            'coin_reward' => 'integer',
            'max_score' => 'integer',
        ];
    }

    // ──────────────────────────────────────────────
    // Route model binding via classwork_items.slug
    // ──────────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getRouteKey(): string|int
    {
        return $this->classworkItem?->slug ?? $this->getKey();
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field === 'slug' || $field === null) {
            return static::whereHas('classworkItem', fn ($q) => $q->where('slug', $value))->first()
                ?? (is_numeric($value) ? static::find($value) : null);
        }
        return static::where($field, $value)->first();
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function attendanceSession(): HasOne
    {
        return $this->hasOne(AttendanceSession::class);
    }

    // ──────────────────────────────────────────────
    // Proxy accessors for shared fields (CTI pattern)
    // ──────────────────────────────────────────────

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

    public function getDescriptionAttribute(): ?string
    {
        return $this->classworkItem?->description;
    }

    /**
     * topic accessor: returns the topic name from the related Topic model via classwork_items.
     */
    public function getTopicAttribute(): ?string
    {
        return $this->classworkItem?->topic?->name;
    }

    /**
     * topic_id accessor for completeness.
     */
    public function getTopicIdAttribute(): ?int
    {
        return $this->classworkItem?->topic_id;
    }

    public function getClassroomAttribute(): ?Classroom
    {
        return $this->classworkItem?->classroom;
    }

    public function getUserAttribute(): ?User
    {
        return $this->classworkItem?->user;
    }

    // ... [keep all existing type helpers, due-date helpers, submission helpers, scopes, allowedSubmissionMimes]
}
```

**IMPORTANT:** Keep all existing methods below the proxy accessors (type helpers, overdue helpers, submission queries, scopes, `allowedSubmissionMimes`). Do NOT delete them.

---

## Task 4: Update `Material` Model

**Files:**
- Modify: `app/Models/Material.php`

**Key changes:**
1. Remove `BelongsToClassroom` and `HasSlug` traits
2. Remove from `$fillable`: `user_id`, `classroom_id`, `title`, `slug`, `description`, `topic_id`
3. Add to `$fillable`: `classwork_item_id`
4. Remove `topic()` BelongsTo (now via `classworkItem()->topic`)
5. Add `classworkItem()` BelongsTo
6. Add same proxy accessors as Assignment (plus `topic_id` and `topic` name proxy)
7. Add route binding methods (same pattern as Assignment)

```php
<?php

namespace App\Models;

use App\Models\Traits\HasCommentsAndAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasCommentsAndAttachments, HasFactory;

    protected $fillable = [
        'classwork_item_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getRouteKey(): string|int
    {
        return $this->classworkItem?->slug ?? $this->getKey();
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field === 'slug' || $field === null) {
            return static::whereHas('classworkItem', fn ($q) => $q->where('slug', $value))->first()
                ?? (is_numeric($value) ? static::find($value) : null);
        }
        return static::where($field, $value)->first();
    }

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

    // Proxy accessors
    public function getClassroomIdAttribute(): ?int { return $this->classworkItem?->classroom_id; }
    public function getUserIdAttribute(): ?int { return $this->classworkItem?->user_id; }
    public function getTitleAttribute(): ?string { return $this->classworkItem?->title; }
    public function getSlugAttribute(): ?string { return $this->classworkItem?->slug; }
    public function getDescriptionAttribute(): ?string { return $this->classworkItem?->description; }
    public function getTopicIdAttribute(): ?int { return $this->classworkItem?->topic_id; }
    public function getTopicAttribute(): ?string { return $this->classworkItem?->topic?->name; }
    public function getClassroomAttribute(): ?Classroom { return $this->classworkItem?->classroom; }
    public function getUserAttribute(): ?User { return $this->classworkItem?->user; }
}
```

---

## Task 5: Update `Classroom` Model — `assignments()` relationship

**Files:**
- Modify: `app/Models/Classroom.php`

**Change:** `assignments()` HasMany must go through `classwork_items`.

Replace:
```php
public function assignments(): HasMany
{
    return $this->hasMany(Assignment::class)->latest();
}
```

With:
```php
public function classworkItems(): HasMany
{
    return $this->hasMany(ClassworkItem::class)->latest();
}

public function assignments(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
{
    return $this->hasManyThrough(
        Assignment::class,
        ClassworkItem::class,
        'classroom_id',   // FK on classwork_items
        'classwork_item_id', // FK on assignments
        'id',             // local key on classrooms
        'id'              // local key on classwork_items
    )->latest('classwork_items.created_at');
}

public function materials(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
{
    return $this->hasManyThrough(
        Material::class,
        ClassworkItem::class,
        'classroom_id',
        'classwork_item_id',
        'id',
        'id'
    )->latest('classwork_items.created_at');
}
```

Add import at top: `use Illuminate\Database\Eloquent\Relations\HasManyThrough;`

**Note:** `->withCount(['assignments'])` used in Dashboard still works with `hasManyThrough`. The `->published()`, `->ofType()`, `->where('type', ...)` scopes on Assignment are still valid since those columns remain on `assignments` table — **except** `->where('topic', ...)` and `->whereNotNull('topic')` in GradeReport which need fixing (see Task 9).

---

## Task 6: Update `Topic` Model — `assignments()` query

**Files:**
- Modify: `app/Models/Topic.php`

Replace:
```php
public function assignments(): Builder
{
    return Assignment::query()
        ->where('classroom_id', $this->classroom_id)
        ->where('topic', $this->name);
}
```

With:
```php
public function assignments(): Builder
{
    return Assignment::query()
        ->whereHas('classworkItem', function ($q) {
            $q->where('classroom_id', $this->classroom_id)
              ->where('topic_id', $this->id);
        });
}
```

---

## Task 7: Update `HasSlug` Trait

**Files:**
- Modify: `app/Models/Traits/HasSlug.php`

The `generateUniqueSlug()` static method currently does `static::where('slug', $slug)->exists()`. After CTI, slug uniqueness must be checked against `classwork_items`. Since both `Assignment` and `Material` use `HasSlug`, we need to make it query `classwork_items`.

**Replace:**
```php
public static function generateUniqueSlug(): string
{
    do {
        $slug = strtolower(Str::random(16));
    } while (static::where('slug', $slug)->exists());

    return $slug;
}
```

**With:**
```php
public static function generateUniqueSlug(): string
{
    do {
        $slug = strtolower(Str::random(16));
    } while (\App\Models\ClassworkItem::where('slug', $slug)->exists());

    return $slug;
}
```

Also remove `bootHasSlug()` since `Assignment` and `Material` no longer have a `slug` column directly — slug is set when creating the `ClassworkItem` (in `Assignment/Create.php` and `Material/Create.php`).

**Note:** `Classroom` has its own `generateUniqueSlug()` method that does NOT use this trait, so it is unaffected.

**Also remove these methods from the trait** since Assignment/Material no longer have slug columns:
- `getRouteKey()` — moved to each model directly
- `resolveRouteBinding()` — moved to each model directly  
- `getRouteKeyName()` — moved to each model directly
- `bootHasSlug()` — no longer needed (slug set on ClassworkItem creation)

The trait can be left as an empty shell or removed from Assignment/Material entirely. Since Classroom does NOT use this trait (it has its own slug logic), and the trait is now only used by Assignment and Material (which both do their own slug binding), the trait can simply be removed from those models. **Keep the trait file but make it empty** to avoid breaking if other models use it.

---

## Task 8: Update `VerifiesContentAccess` Trait

**Files:**
- Modify: `app/Livewire/Concerns/VerifiesContentAccess.php`

Currently: `abort_unless($content->{$fkColumn} === $classroom->id, 404);`

After CTI, `$assignment->classroom_id` still works via the proxy accessor — BUT the accessor returns `?int` from a relationship, which only works if `classworkItem` is loaded. For the IDOR check in `mount()`, we should do a DB query to avoid relying on lazy-loaded relationships.

**Replace:**
```php
protected function verifyContentAccess(Classroom $classroom, Model $content, string $fkColumn = 'classroom_id'): void
{
    abort_unless($content->{$fkColumn} === $classroom->id, 404);
    abort_unless($classroom->hasAccess(auth()->user()), 403);
}
```

**With:**
```php
protected function verifyContentAccess(Classroom $classroom, Model $content, string $fkColumn = 'classroom_id'): void
{
    // For CTI models (Assignment, Material), classroom_id lives on classwork_items
    if ($content instanceof \App\Models\Assignment || $content instanceof \App\Models\Material) {
        abort_unless(
            \App\Models\ClassworkItem::where('id', $content->classwork_item_id)
                ->where('classroom_id', $classroom->id)
                ->exists(),
            404
        );
    } else {
        abort_unless($content->{$fkColumn} === $classroom->id, 404);
    }

    abort_unless($classroom->hasAccess(auth()->user()), 403);
}
```

---

## Task 9: Update `Assignment/Create.php`

**Files:**
- Modify: `app/Livewire/Assignment/Create.php`

**Change:** The DB::transaction block must create a `ClassworkItem` first, then the `Assignment`.

Replace the `DB::transaction(function () use ...)` block:
```php
DB::transaction(function () use ($user, $topicName, $attachments): void {
    $assignment = Assignment::create([
        'user_id' => $user->id,
        'classroom_id' => $this->classroom->id,
        'title' => $this->title,
        'description' => $this->description ? Purifier::clean($this->description) : null,
        'attachments' => ! empty($attachments) ? $attachments : null,
        'max_score' => $this->max_score,
        'exp_reward' => $this->exp_reward,
        'coin_reward' => $this->coin_reward,
        'due_date' => $this->due_date,
        'status' => $this->status,
        'type' => $this->type,
        'topic' => $topicName ?: null,
        'allow_late_submission' => $this->allow_late_submission,
    ]);
    // ...
```

**With:**
```php
DB::transaction(function () use ($user, $topicName, $topicId, $attachments): void {
    $classworkItem = \App\Models\ClassworkItem::create([
        'type' => 'assignment',
        'classroom_id' => $this->classroom->id,
        'user_id' => $user->id,
        'topic_id' => $topicId,
        'title' => $this->title,
        'slug' => \App\Models\Traits\HasSlug::generateUniqueSlug(),
        'description' => $this->description ? Purifier::clean($this->description) : null,
    ]);

    $assignment = Assignment::create([
        'classwork_item_id' => $classworkItem->id,
        'attachments' => ! empty($attachments) ? $attachments : null,
        'max_score' => $this->max_score,
        'exp_reward' => $this->exp_reward,
        'coin_reward' => $this->coin_reward,
        'due_date' => $this->due_date,
        'status' => $this->status,
        'type' => $this->type,
        'allow_late_submission' => $this->allow_late_submission,
    ]);
    // ... rest unchanged
```

**Also fix** the `resolveOrCreateTopic()` call — it currently discards the return value. Capture it:
```php
// Before transaction:
$topicName = trim($this->topic);
$topicId = null;
if ($topicName) {
    $topicId = $this->resolveOrCreateTopic($topicName, $this->classroom->id);
}
```

Add `ClassworkItem` import: `use App\Models\ClassworkItem;`
Add `HasSlug` import: `use App\Models\Traits\HasSlug;`

---

## Task 10: Update `Material/Create.php`

**Files:**
- Modify: `app/Livewire/Material/Create.php`

Replace `Material::create([...])` with:
```php
use Illuminate\Support\Facades\DB;
use App\Models\ClassworkItem;
use App\Models\Traits\HasSlug;

// In save():
DB::transaction(function () use ($user, $topicId): void {
    $classworkItem = ClassworkItem::create([
        'type' => 'material',
        'classroom_id' => $this->classroom->id,
        'user_id' => $user->id,
        'topic_id' => $topicId,
        'title' => $this->title,
        'slug' => HasSlug::generateUniqueSlug(),
        'description' => $this->description ? Purifier::clean($this->description) : null,
    ]);

    $material = Material::create([
        'classwork_item_id' => $classworkItem->id,
    ]);

    // Upload files
    foreach ($this->uploadedFiles as $uploaded) {
        $path = $uploaded['file']->store(
            'materials/attachments/'.$this->classroom->id,
            's3'
        );
        $material->attachments()->create([
            'file_name' => $uploaded['name'],
            'file_path' => $path,
            'file_type' => $uploaded['mime'],
            'file_size' => $uploaded['size'],
            'uploaded_by' => $user->id,
        ]);
    }

    session()->flash('message', __('Material created successfully.'));

    $this->redirect(
        route('material.show', ['classroom' => $this->classroom, 'material' => $material]),
        navigate: true
    );
});
```

Also capture `$topicId` before the transaction:
```php
$topicName = trim($this->topic);
$topicId = $topicName ? $this->resolveOrCreateTopic($topicName, $this->classroom->id) : null;
```

---

## Task 11: Update `Assignment/Show.php` — `saveAssignment()` + IDOR check

**Files:**
- Modify: `app/Livewire/Assignment/Show.php`

**Change 1:** The `editableFields()` map currently includes `'editTopic' => 'topic'`. After CTI, `topic` is a proxy accessor — it reads the topic name. Updates need to go to `classworkItem`. 

**Change 2:** `$this->assignment->update([...])` currently updates `title`, `description`, `topic`. These must now update `classworkItem` (for shared fields) and `assignment` (for assignment-specific fields) separately.

Replace the `saveAssignment()` method's update block:
```php
// Old:
$this->assignment->update([
    'title' => $this->editTitle,
    'description' => $this->editDescription ? Purifier::clean($this->editDescription) : null,
    'max_score' => $this->editMaxScore,
    'exp_reward' => $this->editExpReward,
    'coin_reward' => $this->editCoinReward,
    'due_date' => $this->editDueDate,
    'status' => $this->editStatus,
    'type' => $this->editType,
    'topic' => $topicName ?: null,
    'allow_late_submission' => $this->editAllowLateSubmission,
]);

// New:
$this->assignment->classworkItem->update([
    'title' => $this->editTitle,
    'description' => $this->editDescription ? Purifier::clean($this->editDescription) : null,
    'topic_id' => $topicName ? $this->resolveOrCreateTopic($topicName, $this->classroom->id) : null,
]);

$this->assignment->update([
    'max_score' => $this->editMaxScore,
    'exp_reward' => $this->editExpReward,
    'coin_reward' => $this->editCoinReward,
    'due_date' => $this->editDueDate,
    'status' => $this->editStatus,
    'type' => $this->editType,
    'allow_late_submission' => $this->editAllowLateSubmission,
]);
```

**Remove** the old standalone `resolveOrCreateTopic()` call that precedes the update (it was duplicated; now it's inside the update block).

**Also fix `editableFields()`:** Since `topic` is a proxy accessor (read-only via `$model->topic`), `syncEditFields()` will call `$assignment->topic` which works. But the field-to-attribute map `'editTopic' => 'topic'` will trigger `$model->topic = value` on sync — that won't work since there's no `setTopicAttribute` setter. Either:
- Add `setTopicAttribute()` setter on Assignment that calls `$this->classworkItem->topic_id = ...` (complex, avoid)
- Or remove `'editTopic'` from `editableFields()` and handle topic sync manually in `openEditTab()`

**Recommended:** Override `syncEditFields()` in `Assignment/Show.php` to handle `editTopic` manually, OR simply set `$this->editTopic` in `mount()` directly. Add to `mount()`:
```php
$this->editTopic = $assignment->topic ?? '';
```

And change `editableFields()` to exclude `editTopic`:
```php
protected function editableFields(): array
{
    return [
        'editTitle' => 'title',
        'editDescription' => 'description',
        'editMaxScore' => 'max_score',
        'editExpReward' => 'exp_reward',
        'editCoinReward' => 'coin_reward',
        'editDueDate' => 'due_date',
        'editStatus' => 'status',
        'editType' => 'type',
        'editAllowLateSubmission' => 'allow_late_submission',
    ];
}
```

**Note:** Proxy accessors for `title` and `description` on `Assignment` are read-only getters — `syncEditFields()` calls `$model->{$attribute}` which works fine for reading.

---

## Task 12: Update `Material/Show.php` — `saveMaterial()`

**Files:**
- Modify: `app/Livewire/Material/Show.php`

Similar to Assignment/Show. Replace `$this->material->update([...])` with:
```php
$this->material->classworkItem->update([
    'title' => $this->editTitle,
    'description' => $this->editDescription ? Purifier::clean($this->editDescription) : null,
    'topic_id' => $topicName ? $this->resolveOrCreateTopic($topicName, $this->classroom->id) : null,
]);
```

Also remove `'editTopic' => ''` from `editableFields()` (the empty string attribute is a no-op anyway) and handle `editTopic` init via `mount()` or override.

---

## Task 13: Update `Assignment/Grade.php` — IDOR check

**Files:**
- Modify: `app/Livewire/Assignment/Grade.php`

Replace:
```php
if ($assignment->classroom_id !== $classroom->id) {
    abort(404);
}
```

With:
```php
abort_unless(
    \App\Models\ClassworkItem::where('id', $assignment->classwork_item_id)
        ->where('classroom_id', $classroom->id)
        ->exists(),
    404
);
```

---

## Task 14: Update `Classroom/Show.php` — `deleteAssignment()`

**Files:**
- Modify: `app/Livewire/Classroom/Show.php` (line ~211)

Find:
```php
$assignment = \App\Models\Assignment::where('classroom_id', $this->classroom->id)
```

Replace with:
```php
$assignment = \App\Models\Assignment::whereHas('classworkItem', fn ($q) => $q->where('classroom_id', $this->classroom->id))
```

---

## Task 15: Update `Classroom/GradeReport.php` — topic filter + topic listing

**Files:**
- Modify: `app/Livewire/Classroom/GradeReport.php`

**Change 1:** `getAssignments()` has `->when($this->filterTopic, fn ($q) => $q->where('topic', $this->filterTopic))`. Since `assignments` no longer has `topic`, filter via join:

```php
->when($this->filterTopic, function ($q) {
    $q->whereHas('classworkItem', fn ($q2) => $q2->whereHas('topic', fn ($q3) => $q3->where('name', $this->filterTopic)));
})
```

**Change 2:** `getTopicsProperty()` does:
```php
return $this->classroom->assignments()
    ->published()
    ->whereNotNull('topic')
    ->where('topic', '!=', '')
    ->distinct()
    ->pluck('topic');
```

Replace with:
```php
public function getTopicsProperty(): Collection
{
    return \App\Models\Topic::where('classroom_id', $this->classroom->id)
        ->whereHas('assignments', function ($q) {
            $q->whereHas('classworkItem', fn ($q2) => $q2->where('classroom_id', $this->classroom->id));
        })
        ->pluck('name');
}
```

---

## Task 16: Update `Dashboard.php` — classroom_id queries

**Files:**
- Modify: `app/Livewire/Dashboard.php`

**Change 1** (line 58-67): Assignment query for student analytics uses `->whereIn('classroom_id', $classroomIds)`. Replace with:
```php
$assignments = Assignment::query()
    ->with([
        'classworkItem.classroom.themeCategory',
        'submissions' => fn ($query) => $query->where('user_id', $user->id),
    ])
    ->whereHas('classworkItem', fn ($q) => $q->whereIn('classroom_id', $classroomIds))
    ->published()
    ->whereNotIn('type', ['material', 'announcement', 'topic'])
    ->orderBy('due_date')
    ->get();
```

**Change 2** (line 99): `->groupBy('classroom_id')` on the collection. After CTI, `$assignment->classroom_id` works via proxy accessor. This still works.

**Change 3** (line 110): `$firstAssignment->classroom?->name` — `classroom` proxy accessor works.

**Change 4** (line 113): `$firstAssignment->classroom?->themeCategory?->color` — works via proxy.

**Change 5** (line 194): 
```php
$assignmentIds = \App\Models\Assignment::whereIn('classroom_id', $ownedClassrooms->pluck('id'))->pluck('id');
```
Replace with:
```php
$assignmentIds = \App\Models\Assignment::whereHas('classworkItem', fn ($q) => $q->whereIn('classroom_id', $ownedClassrooms->pluck('id')))->pluck('id');
```

---

## Task 17: Update `show.blade.php` — `groupBy(fn($a) => $a->topic ...)`

**Files:**
- Check: `resources/views/livewire/classroom/show.blade.php` line 211

```js
$grouped = $assignments->groupBy(fn($a) => $a->topic ?? '__none__');
```

This is JavaScript-side (Alpine `$wire` data), but actually it's a PHP-side Blade inline script. `$assignment->topic` works via the proxy accessor, so this **requires no change** — as long as `classworkItem` is eager-loaded when assignments are fetched for the view.

**Check that Classroom/Show.php loads assignments with eager loading:**
```php
$this->classroom->load(['assignments.classworkItem.topic', ...]);
```

Or in the relationship query add `->with('classworkItem.topic')`.

---

## Task 18: Eager Loading Audit

All places that call `$classroom->assignments()` or `Assignment::query()` must eager-load `classworkItem` (and optionally `classworkItem.topic`, `classworkItem.classroom`) to avoid N+1 queries.

**Audit list:**
- `Classroom/Show.php` — `loadRelations()` method
- `Dashboard.php` — both `$assignments` queries (already updated in Task 16)
- `GradeReport.php` — `getAssignments()` — add `->with('classworkItem.topic')`
- Any view that accesses `$assignment->title`, `->topic`, `->classroom_id`, etc.

---

## Task 19: Update Factory (if it exists)

**Files:**
- Check: `database/factories/AssignmentFactory.php`
- Check: `database/factories/MaterialFactory.php`

If factories exist, they must create a `ClassworkItem` first, then the `Assignment`/`Material`. Update their `definition()` methods.

---

## Task 20: Update `new-erd.md`

**Files:**
- Modify: `docs/new-erd.md`

Add `classwork_items` table to the ERD with its columns and FK relationships. Update `assignments` and `materials` to show only their remaining columns.

---

## Task 21: Run Tests

```bash
php artisan test
```

Fix any failures. Pay attention to:
- Factory-based tests that create Assignment/Material directly
- Any test that accesses shared fields
- IDOR security tests in `tests/Feature/SecurityTest.php`

---

## Task 22: Run Pint

```bash
./vendor/bin/pint
```

---

## Execution Order

1 → 2 → 3 → 4 → 5 → 6 → 7 → 8 → 9 → 10 → 11 → 12 → 13 → 14 → 15 → 16 → 17 → 18 → 19 → 20 → 21 → 22

Tasks 3 and 4 (Assignment + Material model) can be done in parallel with Tasks 5+6 after Task 2.
Tasks 9-16 can begin after Tasks 3-8 are done.
