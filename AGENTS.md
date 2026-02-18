# LiteLearning - AI Agent Guide

> This file contains essential information for AI coding agents working on the LiteLearning project.

## Project Overview

**LiteLearning** is a Learning Management System (LMS) inspired by Google Classroom. It provides a modern, interactive platform for classroom management, assignment distribution, real-time collaboration, and gamified learning experiences.

The project is built with a focus on Thai educational institutions (see `Collage.md` for the comprehensive list of supported Thai universities and colleges).

## Technology Stack

### Backend
- **PHP**: 8.2+
- **Framework**: Laravel 12.x
- **Architecture**: Livewire v4 for reactive components (no separate API)
- **Authentication**: Laravel's built-in session-based auth

### Frontend
- **CSS Framework**: TailwindCSS v4 with Vite integration
- **JavaScript**: Vanilla JS + Alpine.js (via Livewire)
- **Build Tool**: Vite
- **Icons**: FontAwesome 7
- **Animations**: Animate.css, custom 3D button effects

### Database & Storage
- **Default Database**: SQLite (for development)
- **Production Database**: MySQL/MariaDB (configured via env)
- **File Storage**: Local or AWS S3 / MinIO (see `docker-compose.s3.yml`)
- **Cache/Queue/Session**: Database driver (configurable)

### Key Packages
- `livewire/livewire` - Reactive components
- `league/flysystem-aws-s3-v3` - S3 storage support
- `sortablejs` - Drag-and-drop sidebar ordering

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Standard HTTP controllers (minimal)
│   │   └── Middleware/         # Custom middleware (auth, setup, locale)
│   ├── Livewire/               # Livewire components (main UI logic)
│   │   ├── Auth/               # Login, Register, Setup
│   │   ├── Classroom/          # Index, Show, Create, Join, People
│   │   ├── Assignment/         # Create, Show, Grade
│   │   ├── Dashboard.php
│   │   └── Settings.php
│   ├── Models/                 # Eloquent models with relationships
│   └── Services/               # Business logic (GamificationService)
├── database/
│   ├── factories/              # Model factories for testing
│   └── migrations/             # Schema migrations
├── resources/
│   ├── views/
│   │   ├── layouts/            # app.blade.php (sidebar + top navbar)
│   │   ├── livewire/           # Livewire component views
│   │   └── pages/              # Static pages (calendar, profile, etc.)
│   ├── css/app.css             # Tailwind v4 with custom 3D button styles
│   └── js/app.js               # Sidebar sortable, Livewire event handlers
├── routes/
│   └── web.php                 # All routes (Livewire components)
├── tests/
│   ├── Feature/                # Feature tests (security, flows)
│   └── Unit/                   # Unit tests
├── lang/                       # Localization (Thai default)
└── docs/                       # Additional documentation
```

## Core Models & Relationships

### User
- Roles: `student`, `teacher`, `admin`
- Relationships: `ownedClassrooms`, `enrolledClassrooms`, `submissions`, `achievements`, `badges`
- Gamification: `coins`, `xp`, `level`
- Setup flow: `setup_completed_at`, onboarding fields

### Classroom
- Unique `slug` (route key) and `code` (for joining)
- Relationships: `teacher`, `members`, `announcements`, `assignments`, `topics`
- Theme color and cover image support

### Assignment
- Types: `assignment`, `quiz`, `material`
- Status: `draft`, `published`
- Relationships: `classroom`, `submissions`, `quizQuestions`, `attachments`

### Polymorphic Relations
- `Comment` -> `commentable` (announcements, assignments)
- `Attachment` -> `attachable` (announcements, assignments, submissions)

See `er-diagram.md` for complete entity relationship diagram.

## Development Commands

### Setup (Initial Installation)
```bash
composer run setup
```
This runs: composer install, .env copy, key generate, migrate, npm install, npm build

### Development Server
```bash
composer run dev
```
Starts concurrently: PHP server, queue worker, Pail (log viewer), Vite dev server

### Testing
```bash
# Run all tests
composer run test

# Or directly
php artisan test

# With coverage (if configured)
php artisan test --coverage
```

### Code Style
```bash
# Laravel Pint (PHP CS Fixer)
./vendor/bin/pint

# Check only
./vendor/bin/pint --test
```

### Storage (S3/MinIO for local dev)
```bash
docker-compose -f docker-compose.s3.yml up -d
```
Access MinIO Console at http://localhost:9001 (minioadmin/minioadmin)

## Testing Strategy

### Test Structure
- **Feature Tests**: Focus on security (IDOR, authorization), user flows
- **Unit Tests**: Model methods, service classes
- **Factories**: Available for User, Classroom, Assignment, Announcement

### Key Security Tests (`tests/Feature/SecurityTest.php`)
- IDOR protection for assignments/submissions across classrooms
- Cross-classroom announcement deletion prevention
- Comment injection protection
- Role-based access control (students can't create classrooms)
- Login rate limiting

### Running Specific Tests
```bash
php artisan test --filter=SecurityTest
php artisan test --filter=SetupFlowTest
```

## Code Style Guidelines

### PHP / Laravel
- PSR-12 compliance (enforced via Laravel Pint)
- Type hints required for method parameters and returns
- Use Laravel's `??` operator and collection methods
- Prefer Eloquent relationships over raw queries
- Use `$fillable` on models (not `$guarded`)

### Livewire Components
- Use `#[Layout('layouts.app')]` attribute for page components
- Public properties for form inputs, use validation rules
- Emit events for cross-component communication: `sidebar-classroom-pinned-updated`
- Use `mount()` for initialization, `render()` for view data

### Blade Templates
- Use TailwindCSS utility classes
- Component-based organization
- Localization: `{{ __('Key Name') }}`
- Icons: FontAwesome classes `<i class="fas fa-icon"></i>`

### CSS (Tailwind v4)
- Custom theme in `app.css`: fonts (Google Sans, Noto Sans Thai)
- 3D button styles: `btn-3d`, color variants (`btn-3d--indigo`, etc.)
- Custom scrollbar styling
- Alpine.js cloak: `[x-cloak] { display: none !important; }`

## Security Considerations

### Authorization Patterns
- **Middleware**: `auth`, `setup` (custom - ensures onboarding complete)
- **Role Checks**: `$user->isTeacher()`, `$user->isStudent()`, `$user->isAdmin()`
- **Ownership**: Always verify `classroom->isOwnedBy($user)` or `classroom->hasAccess($user)`
- **IDOR Prevention**: Validate parent-child relationships (e.g., assignment belongs to classroom)

### Critical Security Checks
When modifying routes or Livewire components that accept IDs:
1. Verify the resource belongs to the parent (e.g., assignment->classroom_id matches URL classroom)
2. Check user has access to the parent resource
3. Return 404 (not 403) for unauthorized access to prevent ID enumeration

### Example Security Pattern
```php
// In Livewire component
public function mount(Classroom $classroom, Assignment $assignment): void
{
    // Prevent IDOR - ensure assignment belongs to this classroom
    if ($assignment->classroom_id !== $classroom->id) {
        abort(404);
    }
    
    // Check access
    if (!$classroom->hasAccess(Auth::user())) {
        abort(403);
    }
}
```

## Gamification System

Managed via `GamificationService`:

### Coins
- Awarded for: creating classrooms, joining classrooms, creating assignments, turning in assignments
- Source tracking via `coin_transactions` table

### XP & Levels
- XP awarded for various activities
- Level formula: Based on cumulative XP (100 XP base per level)
- Level up bonuses: 20 coins per level

### Achievements & Badges
- `achievements`: coin_reward, xp_reward, is_active
- `badges`: Visual rewards with colors
- Tracked via pivot tables with timestamps

## Localization

- **Default Locale**: Thai (`th`)
- **Fallback**: English (`en`)
- **Timezone**: Asia/Bangkok
- Translation files in `lang/` directory

## Configuration Notes

### Environment Variables
Key `.env` settings:
- `APP_LOCALE=th` - Default language
- `APP_FORCE_HTTPS=false` - Set true for production
- `DB_CONNECTION=sqlite` - Switch to `mysql` for production
- `FILESYSTEM_DISK=local` - Set to `s3` for S3/MinIO

### Route Model Binding
- Classroom uses `slug` as route key (not ID)
- This provides user-friendly URLs: `/c/{slug}/...`

## Common Development Tasks

### Adding a New Livewire Component
1. Create class in `app/Livewire/...`
2. Create view in `resources/views/livewire/...`
3. Add route in `routes/web.php`
4. Add to sidebar navigation in `resources/views/layouts/app.blade.php` if needed

### Adding a Database Migration
```bash
php artisan make:migration add_column_to_table
```
Note: Existing migrations include Laravel defaults + one custom migration for user onboarding fields.

### Adding a New Factory
Place in `database/factories/`, extend `Illuminate\Database\Eloquent\Factories\Factory`

## Troubleshooting

### Common Issues
- **Assets not loading**: Run `npm run build`
- **Route not found**: Clear cache `php artisan route:clear`
- **Permission errors**: Ensure `storage/` and `bootstrap/cache/` are writable
- **Sidebar not updating**: Check Livewire event listeners in `app.js`

### Debug Tools
- Laravel Pail: Included in `composer run dev` - shows real-time logs
- Telescope/Tinker: Available if installed
- Debug mode: Set `APP_DEBUG=true` in `.env`

---

*Last updated: Based on codebase analysis as of 2026-02-18*
