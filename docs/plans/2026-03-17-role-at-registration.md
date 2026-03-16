# Role at Registration Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Move role selection (นักเรียน/ครูผู้สอน) from the `/setup` page to the registration form, and remove the entire setup flow and `setup_completed_at` column.

**Architecture:** Add a role radio to Step 1 of the registration form; persist it into `email_otp_verifications.user_data`; use it when creating the User in `verifyOtp()`. Remove the Setup Livewire component, its route, its middleware, and the `setup_completed_at` DB column. Update any code that reads/writes `setup_completed_at`.

**Tech Stack:** Laravel 12, Livewire v4, Blade, Tailwind v4, SQLite (tests), MySQL (prod)

---

## Affected Files

| File | Action |
|------|--------|
| `app/Livewire/Auth/Register.php` | Modify — add `$role` property, validation, store in user_data, use on create, redirect to dashboard |
| `resources/views/livewire/auth/register.blade.php` | Modify — add role radio buttons to Step 1 |
| `app/Livewire/Auth/Setup.php` | **Delete** |
| `resources/views/livewire/auth/setup.blade.php` | **Delete** |
| `app/Http/Middleware/EnsureUserHasCompletedSetup.php` | **Delete** |
| `bootstrap/app.php` | Modify — remove `setup` middleware alias |
| `routes/web.php` | Modify — remove setup route and `setup` middleware group wrapper |
| `app/Models/User.php` | Modify — remove `setup_completed_at` from `$fillable`, `casts()`, remove `needsSetup()` method |
| `database/migrations/` | Create — drop `setup_completed_at` from `users` |
| `database/factories/UserFactory.php` | Modify — remove `setup_completed_at` from default state |
| `database/seeders/DatabaseSeeder.php` | Modify — remove `setup_completed_at` from all User::create calls |
| `tests/Feature/SetupFlowTest.php` | **Delete** (tests a deleted feature) |
| `tests/Feature/SubmissionTest.php` | Modify — remove `setup_completed_at` from factory overrides |
| `tests/Feature/GamificationTest.php` | Modify — remove `setup_completed_at` from factory overrides |
| `database/seeders/LeaderboardSeeder.php` | Modify — remove `setup_completed_at` |

---

### Task 1: Add role field to Register.php and register.blade.php

**Files:**
- Modify: `app/Livewire/Auth/Register.php`
- Modify: `resources/views/livewire/auth/register.blade.php`

**Step 1: Modify Register.php**

Add `public string $role = 'student';` property in the Step 1 properties block.

Add `'role' => 'required|in:student,teacher'` to the `register()` validation rules.

In `sendOtp()`, add `'role' => $this->role` to the `user_data` array inside `EmailOtpVerification::create()`.

In `verifyOtp()`, change `'role' => 'student'` to `'role' => $userData['role']`.

In `verifyOtp()`, change `$this->redirect(route('setup'), navigate: true)` to `$this->redirectRoute('dashboard', navigate: true)`.

Also remove `'email_verified_at' => now()` from the `User::create()` call — it's a dead column (email is already verified via OTP).

**Step 2: Modify register.blade.php**

Insert a role selection block between the "Confirm Password" field and the submit button in Step 1. Use two clickable card-style labels (one for นักเรียน, one for ครูผู้สอน). Example structure:

```blade
{{-- Role --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">ฉันเป็น</label>
    <div class="grid grid-cols-2 gap-3">
        <label class="relative cursor-pointer">
            <input type="radio" wire:model="role" value="student" class="sr-only peer">
            <div class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-colors
                border-gray-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                <i class="fas fa-user-graduate text-2xl mb-1 text-gray-500 peer-checked:text-indigo-600"></i>
                <span class="text-sm font-medium text-gray-700">นักเรียน</span>
            </div>
        </label>
        <label class="relative cursor-pointer">
            <input type="radio" wire:model="role" value="teacher" class="sr-only peer">
            <div class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-colors
                border-gray-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                <i class="fas fa-chalkboard-teacher text-2xl mb-1 text-gray-500 peer-checked:text-indigo-600"></i>
                <span class="text-sm font-medium text-gray-700">ครูผู้สอน</span>
            </div>
        </label>
    </div>
    @error('role')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
```

**Note on Tailwind peer:** The `peer` + `peer-checked:` classes require the hidden radio input to be a sibling before the styled div — the `<input>` must come before the `<div>` inside the same `<label>`. Tailwind v4 supports this natively.

**Step 3: Run tests**

```bash
php artisan test --filter=RegisterTest
```
(If no RegisterTest exists, run `php artisan test` and ensure no regressions.)

**Step 4: Run pint**

```bash
./vendor/bin/pint app/Livewire/Auth/Register.php
```

**Step 5: Commit**

```bash
git add app/Livewire/Auth/Register.php resources/views/livewire/auth/register.blade.php
git commit -m "feat: add role selection to registration form

Move role picker (นักเรียน/ครูผู้สอน) to Step 1 of registration.
Store role in email_otp_verifications.user_data and use it when
creating the user. After OTP verification, redirect to dashboard
instead of /setup."
```

---

### Task 2: Remove Setup component, route, and middleware

**Files:**
- Delete: `app/Livewire/Auth/Setup.php`
- Delete: `resources/views/livewire/auth/setup.blade.php`
- Delete: `app/Http/Middleware/EnsureUserHasCompletedSetup.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/web.php`

**Step 1: Delete the three files**

```bash
rm app/Livewire/Auth/Setup.php
rm resources/views/livewire/auth/setup.blade.php
rm app/Http/Middleware/EnsureUserHasCompletedSetup.php
```

**Step 2: Update bootstrap/app.php**

Open `bootstrap/app.php`. Find the middleware aliases block and remove the `'setup'` alias line.
It likely looks like: `'setup' => \App\Http\Middleware\EnsureUserHasCompletedSetup::class,`

**Step 3: Update routes/web.php**

- Remove the `use App\Livewire\Auth\Setup;` import.
- Remove `Route::get('/setup', Setup::class)->name('setup');`.
- Unwrap the `Route::middleware('setup')->group(function () { ... })` block — keep all the routes inside, just remove the middleware wrapper.
- Update the landing page closure: remove the `$user->needsSetup() ? redirect()->route('setup') :` ternary and replace with direct role-based redirect:

```php
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('dashboard');
    }

    return view('welcome');
})->name('landing');
```

**Step 4: Run pint on modified files**

```bash
./vendor/bin/pint bootstrap/app.php routes/web.php
```

**Step 5: Run tests**

```bash
php artisan test
```

**Step 6: Commit**

```bash
git add -A
git commit -m "feat: remove setup flow

Delete Setup Livewire component, view, and EnsureUserHasCompletedSetup
middleware. Remove /setup route and setup middleware group wrapper.
All routes previously guarded by setup middleware are now accessible
directly after auth."
```

---

### Task 3: Remove setup_completed_at from User model and DB

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_drop_setup_completed_at_from_users_table.php`
- Modify: `app/Models/User.php`

**Step 1: Create the migration**

```bash
php artisan make:migration drop_setup_completed_at_from_users_table
```

Content:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('setup_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('setup_completed_at')->nullable();
        });
    }
};
```

**Step 2: Run migration**

```bash
php artisan migrate
```

**Step 3: Update User.php**

- Remove `'setup_completed_at'` from `$fillable`.
- Remove `'setup_completed_at' => 'datetime'` from `casts()`.
- Remove the `needsSetup()` method.

**Step 4: Run pint**

```bash
./vendor/bin/pint app/Models/User.php
```

**Step 5: Run tests**

```bash
php artisan test
```

**Step 6: Commit**

```bash
git add database/migrations/..._drop_setup_completed_at_from_users_table.php app/Models/User.php
git commit -m "feat: drop setup_completed_at column from users table

Remove setup_completed_at from User model fillable, casts, and delete
the needsSetup() helper method. Add migration to drop the column."
```

---

### Task 4: Clean up factories, seeders, and test files

**Files:**
- Modify: `database/factories/UserFactory.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Modify: `database/seeders/LeaderboardSeeder.php`
- Modify: `tests/Feature/SubmissionTest.php`
- Modify: `tests/Feature/GamificationTest.php`
- Delete: `tests/Feature/SetupFlowTest.php`

**Step 1: Update UserFactory.php**

Remove `'setup_completed_at' => now()` from the `definition()` array.

**Step 2: Update DatabaseSeeder.php**

Remove all three `'setup_completed_at' => now()` lines from User::create calls.

**Step 3: Update LeaderboardSeeder.php**

Remove `'setup_completed_at' => now()` from User create calls.

**Step 4: Update SubmissionTest.php**

Remove `'setup_completed_at' => now()` from the factory calls on lines 29, 30, and 182.

**Step 5: Update GamificationTest.php**

Remove `'setup_completed_at' => now()` from factory calls on lines 28 and 79.

**Step 6: Delete SetupFlowTest.php**

```bash
rm tests/Feature/SetupFlowTest.php
```

**Step 7: Run full test suite**

```bash
php artisan test
```

All tests must pass.

**Step 8: Run pint**

```bash
./vendor/bin/pint
```

**Step 9: Commit**

```bash
git add -A
git commit -m "chore: remove setup_completed_at from factories, seeders, and tests

Remove all references to the dropped setup_completed_at column. Delete
SetupFlowTest as the tested feature no longer exists."
```
