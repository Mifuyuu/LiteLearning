# Register Page — Replace Role Selector with daisyUI Toggle Switch

## Summary
Move role selection (student/teacher) from a radio-card grid in the form body to a daisyUI toggle switch beside the page title. Remove route parameter `{role?}`.

## Changes

### 1. Blade View (`resources/views/livewire/auth/register.blade.php`)
- **Header**: Add daisyUI `toggle` switch on the right side of the title using `flex justify-between items-center`.
  - `<input type="checkbox" class="toggle toggle-primary" wire:model.live="isTeacher">`
  - Title text changes based on `$isTeacher` (student/teacher).
- **Remove**: Role selector radio-card grid (`@if ($showRoleSelector) ... @endif` block).
- **Remove**: `$showRoleSelector` conditions from title display.

### 2. Livewire Component (`app/Livewire/Auth/Register.php`)
- Add computed `$isTeacher` property (getter/setter for `role`):
  - `getIsTeacherProperty()` → `return $this->role === 'teacher'`
  - `setIsTeacherProperty($value)` → `$this->role = $value ? 'teacher' : 'student'`
- Remove `$showRoleSelector` property.
- Remove `mount(?string $role = null)` parameter and logic.

### 3. Route (`routes/web.php`)
- Change `Route::get('/register/{role?}', Register::class)` → `Route::get('/register', Register::class)`.

### 4. Route Reference Cleanup
- Check `Login.php` and other places for `/register/student` or `/register/teacher` links → change to `/register`.