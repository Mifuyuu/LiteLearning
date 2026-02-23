# LiteLearning - AI Agent Guide

> This file contains essential information for AI coding agents working on the LiteLearning project.

## Project Overview

**LiteLearning** is a Learning Management System (LMS) inspired by Google Classroom. It provides a modern, interactive platform for classroom management, assignment distribution, real-time collaboration, and gamified learning experiences.

The project is built with a focus on Thai educational institutions but supports internationalization.

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
- **Rich Text**: Quill.js with image resize module
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
│   │   │   └── SidebarClassroomPreferenceController.php
│   │   └── Middleware/         # Custom middleware (auth, setup, locale)
│   │       ├── EnsureUserHasCompletedSetup.php
│   │       ├── EnsureUserIsAdmin.php
│   │       ├── EnsureUserIsTeacher.php
│   │       └── SetLocale.php
│   ├── Livewire/               # Livewire components (main UI logic)
│   │   ├── Auth/               # Login, Register, Setup
│   │   ├── Classroom/          # Index, Show, Create, Join, People, StreamComment
│   │   ├── Assignment/         # Create, Show, Grade, Attendance
│   │   ├── Student/            # Store, Achievements, Leaderboard
│   │   ├── Dashboard.php
│   │   └── Settings.php
│   ├── Models/                 # Eloquent models with relationships
│   │   ├── User.php
│   │   ├── Classroom.php
│   │   ├── Assignment.php
│   │   ├── Submission.php
│   │   ├── Announcement.php
│   │   ├── Comment.php
│   │   ├── Attachment.php
│   │   ├── QuizQuestion.php
│   │   ├── QuizResponse.php
│   │   ├── AttendanceSession.php
│   │   ├── Topic.php
│   │   ├── UserGamification.php
│   │   ├── CoinTransaction.php
│   │   ├── Achievement.php
│   │   ├── Badge.php
│   │   ├── StoreItem.php
│   │   ├── UserStoreItem.php
│   │   ├── ClassroomSidebarPreference.php
│   │   └── BugReport.php
│   ├── Services/               # Business logic
│   │   └── GamificationService.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── factories/              # Model factories for testing
│   │   ├── UserFactory.php
│   │   ├── ClassroomFactory.php
│   │   ├── AssignmentFactory.php
│   │   └── AnnouncementFactory.php
│   ├── migrations/             # Schema migrations
│   └── seeders/                # Database seeders
├── resources/
│   ├── views/
│   │   ├── layouts/            # app.blade.php (sidebar + top navbar)
│   │   ├── livewire/           # Livewire component views
│   │   ├── components/         # Blade components
│   │   └── pages/              # Static pages (calendar, profile, settings, etc.)
│   ├── css/app.css             # Tailwind v4 with custom 3D button styles
│   ├── js/app.js               # Sidebar sortable, Livewire event handlers
│   └── gamestyleicons.css      # Game-style icon font
├── routes/
│   ├── web.php                 # All web routes (Livewire components)
│   └── console.php             # Artisan commands
├── tests/
│   ├── Feature/                # Feature tests (security, flows)
│   │   ├── SecurityTest.php
│   │   └── SetupFlowTest.php
│   └── Unit/                   # Unit tests
├── lang/                       # Localization (Thai default)
│   └── th/
│       └── validation.php
├── config/                     # Laravel configuration
└── docs/                       # Additional documentation
```

## Core Models & Relationships

### User
- **Roles**: `student`, `teacher`, `admin`
- **Relationships**: 
  - `ownedClassrooms` - Classrooms the user teaches
  - `enrolledClassrooms` - Classrooms the user is enrolled in (pivot: role, joined_at)
  - `submissions` - Assignment submissions
  - `announcements` - Created announcements
  - `assignments` - Created assignments
  - `comments` - Posted comments
  - `gamification` - UserGamification record (coins, XP, level)
  - `achievements` - Unlocked achievements (pivot: unlocked_at)
  - `badges` - Earned badges (pivot: earned_at)
  - `storeItems` - Purchased store items
  - `bugReports` - Submitted bug reports
- **Customizations**: `ui_scale`, `active_name_color`, `active_avatar_frame`
- **Setup flow**: `setup_completed_at`, `tos_accepted_at`, onboarding fields (school_name, study_year, birth_date)

### Classroom
- Unique `slug` (16-char alphanumeric, route key) and `code` (6-char uppercase, for joining)
- **Relationships**: `teacher`, `members` (pivot: role, joined_at), `students`, `announcements`, `assignments`, `topics`
- **Theme**: `theme_color`, `cover_image`
- **Access control**: `isOwnedBy()`, `hasMember()`, `hasAccess()`

### Assignment
- **Types**: `attendance`, `file` (upload), `question`, `quiz`, `material`
- **Status**: `draft`, `published`
- Unique `slug` (16-char alphanumeric, route key)
- **Relationships**: `classroom`, `user` (creator), `submissions`, `quizQuestions`, `attendanceSession`, `comments`, `attachments`
- **Helpers**: `typeIcon()`, `typeColor()`, `typeLabel()`, `isOverdue()`, `canAcceptSubmission()`, `submissionFor()`

### Submission
- **Status**: `assigned`, `turned_in`, `graded`, `returned`
- **Relationships**: `assignment`, `user`, `attachments`, `quizResponses`
- **Helpers**: `turnIn()`, `unsubmit()`, `grade()`, `returnSubmission()`, `isLate()`

### Polymorphic Relations
- `Comment` -> `commentable` (announcements, assignments)
- `Attachment` -> `attachable` (announcements, assignments, submissions)

### Gamification Models
- **UserGamification**: coins, xp, level (linked to users)
- **CoinTransaction**: Tracks all coin earnings/spending with source and reference
- **Achievement**: coin_reward, xp_reward, is_active, code
- **Badge**: Visual rewards with colors and codes
- **StoreItem**: Purchasable items (name_color, avatar_frame types)

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

# Specific test files
php artisan test --filter=SecurityTest
php artisan test --filter=SetupFlowTest
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
- Login rate limiting (5 attempts)

### Setup Flow Tests (`tests/Feature/SetupFlowTest.php`)
- Redirect to setup for incomplete users
- Complete setup process validation

## Code Style Guidelines

### PHP / Laravel
- PSR-12 compliance (enforced via Laravel Pint)
- Type hints required for method parameters and returns
- Use Laravel's `??` operator and collection methods
- Prefer Eloquent relationships over raw queries
- Use `$fillable` on models (not `$guarded`)
- Strict model property typing with casts

### Livewire Components
- Use `#[Layout('layouts.app')]` attribute for page components
- Public properties for form inputs, use validation rules
- Emit events for cross-component communication: `sidebar-classroom-pinned-updated`, `classroom-updated`
- Use `mount()` for initialization, `render()` for view data
- Security: Validate parent-child relationships in mount()

### Blade Templates
- Use TailwindCSS utility classes
- Component-based organization
- Localization: `{{ __('Key Name') }}`
- Icons: FontAwesome classes `<i class="fas fa-icon"></i>`
- Game icons: `<i class="gsi-icon-name"></i>`

### CSS (Tailwind v4)
- Custom theme in `app.css`: fonts (Google Sans, Noto Sans Thai)
- 3D button styles: `btn-3d`, color variants (`btn-3d--indigo`, etc.)
- Custom scrollbar styling
- Alpine.js cloak: `[x-cloak] { display: none !important; }`
- UI scale: applied via inline style `style="zoom: {{ auth()->user()->ui_scale }}%;"`

### JavaScript
- Vanilla JS with SortableJS for drag-and-drop
- Livewire event listeners in `app.js`
- No jQuery dependency

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

### Rate Limiting
- Login rate limited to 5 attempts
- Configured in `RouteServiceProvider` or Login Livewire component

## Gamification System

Managed via `GamificationService`:

### Coins
- Awarded for: creating classrooms (30), joining classrooms (15), creating assignments (20), turning in assignments (10)
- Source tracking via `coin_transactions` table with reference_type/reference_id
- Only students are eligible for gamification rewards

### XP & Levels
- XP awarded for various activities
- Level formula: Based on cumulative XP (100 XP base per level, triangular progression)
- Level up bonuses: 20 coins per level gained

### Achievements & Badges
- `achievements`: coin_reward, xp_reward, is_active, code
- `badges`: Visual rewards with colors, code-based
- Tracked via pivot tables with timestamps

### Store
- Students can purchase name colors and avatar frames
- Items have `type`, `value`, `price`, `is_active`
- Equipped via user fields: `active_name_color`, `active_avatar_frame`

## Localization

- **Default Locale**: Thai (`th`)
- **Fallback**: English (`en`)
- **Timezone**: Asia/Bangkok
- Translation files in `lang/` directory
- Validation messages localized

## Configuration Notes

### Environment Variables
Key `.env` settings:
- `APP_LOCALE=th` - Default language
- `APP_FORCE_HTTPS=false` - Set true for production
- `DB_CONNECTION=sqlite` - Switch to `mysql` for production
- `FILESYSTEM_DISK=local` - Set to `s3` for S3/MinIO
- `QUEUE_CONNECTION=database` - For background jobs
- `CACHE_STORE=database` - For caching

### Route Model Binding
- Classroom uses `slug` as route key (not ID)
- Assignment uses `slug` as route key (not ID)
- This provides user-friendly URLs: `/c/{slug}/a/{slug}`

### Sidebar Preferences
- Stored in `classroom_sidebar_preferences` table
- Supports drag-and-drop ordering with SortableJS
- Events: `sidebar-classroom-pinned-updated`

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
Follow Laravel migration conventions. Existing migrations show the evolution of the schema.

### Adding a New Factory
Place in `database/factories/`, extend `Illuminate\Database\Eloquent\Factories\Factory`

## Assignment Types Reference

| Type | Description | Icon | Color |
|------|-------------|------|-------|
| `attendance` | Attendance check-in | fa-clipboard-check | amber |
| `file` | File upload assignment | fa-cloud-arrow-up | blue |
| `question` | Short answer question | fa-pen-to-square | green |
| `quiz` | Multiple choice quiz | fa-circle-question | purple |
| `material` | Reading material (no submission) | fa-book-open | slate |

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

*Last updated: Based on codebase analysis as of 2026-02-23*
