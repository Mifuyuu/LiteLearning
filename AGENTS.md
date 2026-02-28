# LiteLearning — AI Agent Guide

Learning Management System built with Laravel 12 + Livewire v4. Inspired by Google Classroom, focused on Thai educational institutions.

---

## Commands

### Initial Setup
```bash
composer run setup
# Runs: composer install, .env copy, key:generate, migrate, npm install, npm run build
```

### Development Server
```bash
composer run dev
# Concurrently starts: PHP server, queue worker (tries=1), Vite dev server
```

### Testing
```bash
# Run all tests (clears config first)
composer run test

# Run all tests directly
php artisan test

# Run a single test file
php artisan test tests/Feature/SecurityTest.php

# Run a single test by name (filter matches method name)
php artisan test --filter=test_assignment_show_rejects_assignment_from_different_classroom

# Run a test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# With coverage
php artisan test --coverage
```

### Linting & Code Style
```bash
# Fix PHP code style (Laravel Pint — PSR-12)
./vendor/bin/pint

# Check only, no writes
./vendor/bin/pint --test
```

### Asset Build
```bash
npm run build   # production build
npm run dev     # vite dev server with HMR
```

### Useful Artisan Commands
```bash
php artisan route:clear          # clear route cache after route changes
php artisan migrate              # run new migrations
php artisan make:migration name  # create a new migration
php artisan tinker               # interactive REPL
```

---

## Architecture

- **No REST API.** All UI logic lives in Livewire components (`app/Livewire/`).
- **Route model binding** uses `slug` (not `id`) for `Classroom` and `Assignment`.
- **URLs**: `/c/{classroom-slug}` and `/c/{classroom-slug}/a/{assignment-slug}`
- **Tests** use SQLite in-memory (`DB_DATABASE=:memory:`), `RefreshDatabase` trait.
- **Queue/Cache/Session** use `database` driver in dev, `array` in tests.
- **Locale**: Thai (`th`) default. Fallback: English. Timezone: `Asia/Bangkok`.

---

## PHP / Laravel Style

### General
- **PSR-12** enforced via Laravel Pint (no separate pint.json — uses defaults).
- **4-space indentation**, UTF-8, LF line endings (see `.editorconfig`).
- Type hints on **all** method parameters and return types — no untyped methods.
- Use `$fillable` on models. Never `$guarded`.
- `protected function casts(): array` method form (not `$casts` property).
- Prefer Eloquent relationships and collection methods over raw queries.
- Use `??` operator, `abort_unless()`, and Laravel helpers over manual conditionals.

### Import Ordering
Group `use` statements: App\Models → App\Services → Illuminate facades → Livewire classes. Alphabetical within groups.
```php
use App\Models\Classroom;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
```

### Models
- Explicit return types on all relationships (`HasMany`, `BelongsToMany`, etc.).
- `getRouteKeyName()` overridden to return `'slug'` for routable models.
- Model boot hooks in `protected static function booted(): void`.
```php
public function ownedClassrooms(): HasMany
{
    return $this->hasMany(Classroom::class, 'teacher_id');
}

public function enrolledClassrooms(): BelongsToMany
{
    return $this->belongsToMany(Classroom::class)
        ->withPivot('role', 'joined_at')
        ->withTimestamps();
}
```

---

## Livewire Components

### Class Structure
```php
#[Layout('layouts.app')]   // or layouts.guest for auth pages
class Show extends Component
{
    // 1. Typed public properties (form inputs, bound models)
    public Classroom $classroom;
    public string $title = '';
    public bool $showModal = false;
    public ?string $dueDate = null;

    // Use #[Url] to sync a property with the URL query string
    #[Url(as: 'tab', except: 'stream')]
    public string $activeTab = 'stream';

    // 2. mount() — security checks + initialization (no render logic here)
    public function mount(Classroom $classroom): void { ... }

    // 3. Action methods (save, delete, toggle, etc.)
    public function save(): void { ... }

    // 4. render() — always returns a view
    public function render()
    {
        return view('livewire.classroom.show');
    }
}

### Validation
Two valid patterns — use inline `$this->validate([...])` for method-level validation, or `protected $rules` for component-wide rules:
```php
// Method-level (preferred for complex components)
$this->validate([
    'title'   => 'required|string|max:255',
    'dueDate' => 'nullable|date',
]);

// Component-level (simple forms like Login)
protected $rules = [
    'email'    => 'required|email',
    'password' => 'required|min:6',
];
```

### Events
```php
$this->dispatch('sidebar-classroom-pinned-updated');
$this->dispatch('classroom-updated');
```

### Flash Messages
Use `session()->flash()` for one-time success/error feedback after mutations:
```php
session()->flash('message', __('Saved successfully.'));
session()->flash('error', __('Something went wrong.'));
```

### Eager-Loading Helper Pattern
Extract relationship loading into a private helper — call from `mount()` only, never from `render()`:
```php
public function mount(Classroom $classroom): void
{
    abort_unless($classroom->hasAccess(auth()->user()), 403);
    $this->classroom = $classroom;
    $this->loadRelations();
}

private function loadRelations(): void
{
    $this->classroom->load('assignments', 'members');
}

### UI Scale
Apply zoom to page root in every page component view:
```blade
<div style="zoom: {{ auth()->user()->ui_scale }}%;">
```

---

## Security Patterns

**Always** verify parent-child relationships in `mount()` — never trust route parameters alone.

```php
public function mount(Classroom $classroom, Assignment $assignment): void
{
    // 1. IDOR check — assignment must belong to this classroom
    abort_unless(
        \App\Models\ClassroomContent::where('contentable_type', Assignment::class)
            ->where('contentable_id', $assignment->id)
            ->where('classroom_id', $classroom->id)
            ->exists(),
        404   // 404, not 403 — prevents ID enumeration
    );

    // 2. Access check
    abort_unless($classroom->hasAccess(auth()->user()), 403);
}
```

### Rules
- Return **404** (not 403) for unauthorized resource access to prevent ID enumeration.
- Use `abort_unless()` over manual `if + abort()` where possible.
- Role checks: `$user->isTeacher()`, `$user->isStudent()`, `$user->isAdmin()`.
- Ownership: `$classroom->isOwnedBy($user)`, `$classroom->hasAccess($user)`.
- Middleware stack: `auth` → `setup` → role (`teacher` / `student` / `admin`).
- Login rate-limited to 5 attempts per email+IP per minute.

---

## Routing

```php
// Nested middleware groups — order matters
Route::middleware('auth')->group(function () {
    Route::middleware('setup')->group(function () {
        Route::middleware('teacher')->group(function () {
            // teacher-only routes
        });
        Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
            // admin routes
        });
    });
});

// Specific routes BEFORE wildcard segments
Route::get('/c/{classroom}/a/create', AssignmentCreate::class);   // BEFORE:
Route::get('/c/{classroom}/a/{assignment}', AssignmentShow::class);
```

Route names: `dashboard`, `classroom.show`, `assignment.show`, `assignment.grade`, `admin.dashboard`, etc.

---

## Blade / Frontend

### Templates
- Localization: `{{ __('Key Name') }}`
- FontAwesome icons: `<i class="fas fa-icon-name"></i>` (FA 7)
- Game icons: `<i class="gsi-icon-name"></i>` (custom font, `gamestyleicons.css`)
- Alpine.js cloak: always add `[x-cloak]` CSS is in `app.css`; use `x-cloak` on hidden elements.

### CSS (Tailwind v4)
- Config via `@theme {}` in `app.css` — no `tailwind.config.js`.
- Font: Google Sans / Noto Sans Thai (set in `@theme`).
- 3D buttons: `btn-3d`, `btn-3d--indigo`, `btn-3d--red`, `btn-3d--white`, `btn-3d--dark`.
  - `btn-3d--white` — for light backgrounds.
  - `btn-3d--dark` — semi-transparent, for use on colored backgrounds (e.g. indigo CTA blocks).
- Custom scrollbar styles and `.tabs-scroll` utility are pre-defined.

### JavaScript
- Vanilla JS + SortableJS for sidebar drag-and-drop.
- Livewire event listeners registered in `resources/js/app.js`.
- Cropper.js for avatar image cropping.
- **No jQuery.**

---

## Testing

Tests use `RefreshDatabase` + SQLite in-memory. Factories exist for `User`, `Classroom`, `Assignment`, `Announcement`.

```php
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_something(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher);
        $response = $this->get(route('classroom.show', $classroom));
        $response->assertStatus(200);
    }
}
```

- **Feature tests**: security (IDOR, authz), user flows, upload handling.
- **Unit tests**: `tests/Unit/` — currently empty, use for model methods and services.
- Test method names: `test_snake_case_description_of_what_is_tested`.
- Use `/** @var User $user */` PHPDoc before typed `Auth::user()` assignments.

---

## File Storage

Images/files stored via Laravel's `s3` disk pointing to a self-hosted MinIO instance.

```ini
# .env — keep both keys:
AWS_ENDPOINT=http://127.0.0.1:9000   # server → MinIO (internal)
AWS_URL=https://storage.ikmcw.xyz/litelearning  # public URL for browser links
```

- Always use `Storage::url($path)` or `Storage::disk('s3')->url($path)` to generate URLs — never hardcode.
- The `url` key in `config/filesystems.php` `s3` driver must be set to `env('AWS_URL')` so generated links use the public hostname, not `127.0.0.1`.
- MinIO bucket policy for `avatars/` must be public for avatar images to load without signed URLs.

---

## Gamification

Managed via `App\Services\GamificationService`. **Only students** receive rewards — always guard with `$user->isStudent()` before awarding coins/XP.

Coin events: join classroom (15), create assignment (20), turn in assignment (10).
Level-up bonus: 20 coins per level. XP uses triangular progression (100 XP base).

---

## Common Pitfalls

- **Never use `$guarded`** — always explicit `$fillable`.
- **Never suppress types** with `as any` / `@ts-ignore` / `@ts-expect-error`.
- **Always eager-load** relationships in `mount()`, not `render()` — prevents N+1 on every Livewire update.
- **`casts()` method**, not `$casts` property (project-wide pattern).
- **`abort_unless()` over `if (!...) abort()`** — cleaner and consistent.
- After editing routes: `php artisan route:clear`.
- After editing assets: `npm run build` (or use `composer run dev` for HMR).
