# Achievement & Badge System — Implementation Plan

**Feature**: Redesign achievement system (student-only), add 17 achievements, equippable badges in profile, achievement display in profile page, unlock toast notifications.

**Date**: 2026-03-09  
**Status**: Approved for implementation

---

## Context Summary

- **Current state**: GamificationService exists with `isEligible()` (student-only guard). Achievements table exists. 3 teacher-targeted achievements exist (must deactivate). No badge equipping in UI. No registration reward hook. No comment reward hook. No perfect-score reward hook.
- **Schema**: `users` table has `active_name_color`, `active_avatar_frame`. Badge equipping uses a **separate `user_equipped_badges` table** (not columns on users) to avoid sparse NULLs for teacher/admin rows.
- **Style**: Dark space theme (`space-bg`, `card-3d card-3d--purple`), Thai-only locale, btn-3d buttons, FontAwesome 7 icons.
- **Notification**: Toast/popup on achievement unlock.
- **Seeder**: `GamificationFeaturesSeeder` uses `updateOrCreate` — safe to re-run.

---

## Achievement List (Final — Student-Only)

| # | Code | Name (TH) | Condition | Trigger Point | Coins | XP |
|---|------|-----------|-----------|---------------|-------|----|
| 1 | `first_registered` | ยินดีต้อนรับ! | สมัครสมาชิกครั้งแรก | Register.php after Auth::login() | 50 | 100 |
| 2 | `first_classroom_joined` | นักเรียนใหม่ | เข้าห้องเรียนครั้งแรก | awardForClassroomJoined() [exists] | 15 | 25 |
| 3 | `multi_class` | นักเรียนพันธุ์แกร่ง | เข้าร่วม 3+ ห้องเรียน | awardForClassroomJoined() [exists] | 30 | 50 |
| 4 | `social_butterfly` | ผีเสื้อสังคม | เข้าร่วม 5+ ห้องเรียน | awardForClassroomJoined() [exists] | 50 | 80 |
| 5 | `first_assignment_turned_in` | เริ่มต้นดี | ส่งงานครั้งแรก | awardForAssignmentTurnedIn() [exists] | 10 | 20 |
| 6 | `consistent_submitter_10` | ส่งงานสม่ำเสมอ | ส่งงาน 10 ครั้ง | awardForAssignmentTurnedIn() [update] | 30 | 60 |
| 7 | `consistent_submitter_25` | นักส่งงานตัวยง | ส่งงาน 25 ครั้ง | awardForAssignmentTurnedIn() [new] | 60 | 100 |
| 8 | `consistent_submitter_50` | แชมป์ส่งงาน | ส่งงาน 50 ครั้ง | awardForAssignmentTurnedIn() [new] | 100 | 200 |
| 9 | `early_bird` | นกตื่นเช้า | ส่งงานก่อนกำหนด 1+ วัน | awardForAssignmentTurnedIn() [new logic] | 20 | 30 |
| 10 | `midnight_submission` | มนุษย์กลางคืน | ส่งงาน 00:00–00:59 | awardForAssignmentTurnedIn() [new] | 15 | 20 |
| 11 | `noon_submission` | ส่งงานเที่ยงตรง | ส่งงาน 12:00–12:59 | awardForAssignmentTurnedIn() [new] | 15 | 20 |
| 12 | `on_a_roll` | ไฟแรง! | ส่งงาน 3 ชิ้นใน 7 วัน | awardForAssignmentTurnedIn() [new] | 25 | 40 |
| 13 | `perfect_score` | ได้เต็ม! | ได้คะแนนเต็มครั้งแรก | awardForPerfectScore() [new method] | 50 | 80 |
| 14 | `grade_seeker` | นักล่าคะแนน | ได้คะแนนเต็ม 5 ครั้ง | awardForPerfectScore() [new method] | 100 | 150 |
| 15 | `first_comment` | เริ่มพูดคุย | คอมเมนต์ครั้งแรก | awardForCommented() [new method] | 10 | 15 |
| 16 | `chatterbox` | สาวสังคม | คอมเมนต์ 20 ครั้ง | awardForCommented() [new method] | 30 | 50 |
| 17 | `level_up` | เลเวลอัพ! | เลเวลขึ้นครั้งแรก | awardXp() internal [exists] | 20 | 0 |

**Deactivate** (set `is_active = false` in seeder):
- `first_classroom_created`, `classroom_builder`, `first_assignment_created`

**Rename** existing code:
- `consistent_submitter` → `consistent_submitter_10`

---

## New Features

### A. Equippable Badges in Settings
- User can equip up to **3 badges** (from unlocked achievements).
- Stored in a separate `user_equipped_badges` table: `(user_id, slot, achievement_id)` with composite PK `(user_id, slot)`. Slot values: 1, 2, 3.
- Settings page (`livewire/settings.blade.php`) gets a new **"ตราสัญลักษณ์"** section (student-only).
- Shows all unlocked achievements as selectable icons. 3 badge slots — click to equip/unequip.
- Uses `Settings.php` Livewire component (add badge equip logic).

### B. Pinned Badges on Profile Card
- `profile.blade.php` shows 3 badge slots below name/email (uses `user->equippedBadges` relation keyed by slot).
- If badge slot is null → show placeholder with dashed border.
- Badges are small (`w-10 h-10`) with tooltip (achievement name).

### C. Achievement Gallery on Profile Page
- Below stats grid in `profile.blade.php`.
- Grid: `grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-3`
- Each achievement: icon image + name (truncated), tooltip with description.
- **Locked**: grayscale + opacity-40 + lock icon overlay.
- **Unlocked**: full color + green checkmark overlay.
- Only show for student profiles (check profile user's role, not viewer).

### D. Achievement Unlock Toast Notification
- When `unlockAchievement()` succeeds, dispatch a Livewire event: `achievement-unlocked` with `{name, icon, coin_reward, xp_reward}`.
- `resources/js/app.js` listens to `achievement-unlocked` and renders a toast popup.
- Toast style: dark card (space theme), achievement icon on left, name + reward on right, auto-dismiss after 4 seconds, slide-in from bottom-right.

---

## Implementation Tasks (Ordered)

### Step 1 — Migration: Create user_equipped_badges table
**File**: `database/migrations/YYYY_MM_DD_create_user_equipped_badges_table.php`
```php
Schema::create('user_equipped_badges', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->tinyInteger('slot'); // 1, 2, or 3
    $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
    $table->primary(['user_id', 'slot']);
    $table->timestamps();
});
```

### Step 2 — Model: User.php
Add relationship:
```php
public function equippedBadges(): HasMany
{
    return $this->hasMany(UserEquippedBadge::class)->with('achievement');
}
```
No changes needed to `$fillable`.

### Step 2b — New Model: UserEquippedBadge.php
**File**: `app/Models/UserEquippedBadge.php`
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEquippedBadge extends Model
{
    protected $fillable = ['user_id', 'slot', 'achievement_id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function achievement(): BelongsTo { return $this->belongsTo(Achievement::class); }
}
```

### Step 3 — GamificationFeaturesSeeder
- Deactivate teacher achievements (set `is_active=false`): `first_classroom_created`, `classroom_builder`, `first_assignment_created`
- Rename `consistent_submitter` → `consistent_submitter_10`
- Update rewards on existing achievements to match table above
- Add 6 new achievement entries: `first_registered`, `consistent_submitter_25`, `consistent_submitter_50`, `midnight_submission`, `noon_submission`, `first_comment`
- Badge images: use existing `images/achievements/achievements-img-01.svg` as placeholder for all (can be replaced by assets later)
- Run with `php artisan db:seed --class=GamificationFeaturesSeeder`

### Step 4 — GamificationService: New methods

#### `awardForRegistered(User $user): void`
```php
if (!$this->isEligible($user)) return;
$this->awardCoins($user, 50, 'registered', null, null, []);
$this->awardXp($user, 100);
$this->unlockAchievement($user, 'first_registered');
```

#### `awardForCommented(User $user): void`
```php
if (!$this->isEligible($user)) return;
$this->awardCoins($user, 5, 'comment_posted', null, null, []);
$this->awardXp($user, 10);
$count = $user->comments()->count();
if ($count >= 1) $this->unlockAchievement($user, 'first_comment');
if ($count >= 20) $this->unlockAchievement($user, 'chatterbox');
```

#### `awardForPerfectScore(User $user, int $assignmentId, float|int $score, float|int $maxScore): void`
```php
if (!$this->isEligible($user)) return;
if ($maxScore <= 0 || $score < $maxScore) return;
$this->awardCoins($user, 20, 'perfect_score', 'assignment', $assignmentId, []);
$this->awardXp($user, 30);
// Count perfect submissions via join
$perfectCount = $user->submissions()
    ->join('assignments', 'assignments.id', '=', 'submissions.assignment_id')
    ->whereColumn('submissions.score', '>=', 'assignments.max_score')
    ->where('assignments.max_score', '>', 0)
    ->count();
if ($perfectCount >= 1) $this->unlockAchievement($user, 'perfect_score');
if ($perfectCount >= 5) $this->unlockAchievement($user, 'grade_seeker');
```

#### Update `awardForAssignmentTurnedIn(User $user, int $assignmentId)`: add Submission + Assignment params
```php
// Add at end of existing method:
// Time-based checks (Bangkok timezone)
$turnedInAt = $submission->turned_in_at->timezone('Asia/Bangkok');
$hour = (int) $turnedInAt->format('H');
if ($hour === 0) $this->unlockAchievement($user, 'midnight_submission');
if ($hour === 12) $this->unlockAchievement($user, 'noon_submission');

// Early bird
if ($assignment->due_date && $turnedInAt->lt($assignment->due_date->subDay())) {
    $this->unlockAchievement($user, 'early_bird');
}

// On a roll (3 submissions in 7 days)
$weekCount = $user->submissions()
    ->where('status', 'turned_in')
    ->where('turned_in_at', '>=', now()->subDays(7))
    ->count();
if ($weekCount >= 3) $this->unlockAchievement($user, 'on_a_roll');

// Milestones
$totalCount = $user->submissions()->whereIn('status', ['turned_in', 'graded', 'returned'])->count();
if ($totalCount >= 10) $this->unlockAchievement($user, 'consistent_submitter_10');
if ($totalCount >= 25) $this->unlockAchievement($user, 'consistent_submitter_25');
if ($totalCount >= 50) $this->unlockAchievement($user, 'consistent_submitter_50');
```
**Signature change**: `awardForAssignmentTurnedIn(User $user, int $assignmentId, ?Submission $submission = null, ?Assignment $assignment = null)`  
Update callers in `Assignment/Show.php` and `Assignment/Attendance.php` to pass submission and assignment objects.

### Step 5 — Achievement Unlock Toast (Livewire → JS)
In `unlockAchievement()`, after successful pivot attach:
```php
// Dispatch Livewire browser event for toast
Livewire::dispatch('achievement-unlocked', [
    'name' => $achievement->name,
    'description' => $achievement->description,
    'icon' => $achievement->icon,
    'badge_image' => $achievement->badge_image,
    'coin_reward' => $achievement->coin_reward,
    'xp_reward' => $achievement->xp_reward,
]);
```
In `resources/js/app.js`, add listener for `achievement-unlocked` → create + inject toast DOM element, auto-remove after 4s.  
Toast HTML structure: fixed bottom-right, dark bg (`bg-gray-900 border border-indigo-500`), fade-in animation, badge_image on left, name + coins/XP right.

### Step 6 — Register.php: Call awardForRegistered
After `Auth::login($user)`, inject GamificationService and call:
```php
app(\App\Services\GamificationService::class)->awardForRegistered($user);
```

### Step 7 — StreamComment.php: Call awardForCommented
After `Comment::create(...)` in `addComment()`:
```php
app(\App\Services\GamificationService::class)->awardForCommented($user);
```

### Step 8 — Assignment/Grade.php: Call awardForPerfectScore
After `$this->submission->grade($score, $feedback)`:
```php
$grader = auth()->user();
$submissionUser = $this->submission->user;
app(\App\Services\GamificationService::class)->awardForPerfectScore(
    $submissionUser,
    $this->assignment->id,
    $score,
    $this->assignment->max_score
);
```

### Step 9 — Settings.php: Badge equip logic
Add to component:
```php
public array $badgeSlots = [1 => null, 2 => null, 3 => null]; // slot => achievement_id|null
public array $unlockedAchievements = [];

public function mount(): void {
    // existing name load...
    $user = auth()->user();
    if ($user->isStudent()) {
        $equipped = $user->equippedBadges()->get()->keyBy('slot');
        foreach ([1, 2, 3] as $slot) {
            $this->badgeSlots[$slot] = $equipped->get($slot)?->achievement_id;
        }
        $this->unlockedAchievements = $user->achievements()->get()->toArray();
    }
}

public function equipBadge(int $slot, ?int $achievementId): void {
    $user = auth()->user();
    abort_unless($user->isStudent(), 403);
    abort_unless(in_array($slot, [1, 2, 3]), 422);
    if ($achievementId !== null) {
        abort_unless(
            $user->achievements()->where('achievement_id', $achievementId)->exists(),
            403
        );
    }
    if ($achievementId === null) {
        $user->equippedBadges()->where('slot', $slot)->delete();
    } else {
        $user->equippedBadges()->updateOrCreate(
            ['slot' => $slot],
            ['achievement_id' => $achievementId]
        );
    }
    $this->badgeSlots[$slot] = $achievementId;
    session()->flash('message', __('อัพเดทตราสัญลักษณ์แล้ว'));
}
```

### Step 10 — settings.blade.php: Badge equip UI section
Add below account settings card (student-only `@if(auth()->user()->isStudent())`):
- Card: `card-3d card-3d--purple`
- Title: "ตราสัญลักษณ์" with `fas fa-award` icon
- 3 badge slots displayed as clickable circles (show current badge image or empty circle)
- Grid of all unlocked achievements as selectable options
- Click badge in grid → fills selected slot; click slot to clear

### Step 11 — profile.blade.php: Pinned badges display
Add below name/email/role block:
- 3 badge slot `div` in a flex row
- Each: `w-10 h-10 rounded-full border-2 border-dashed border-gray-300`; if filled, show `<img>` with tooltip
- Eager-load `equippedBadges.achievement` in Profile.php mount() via `$this->profileUser->load('equippedBadges.achievement')`
- In blade: `$profileUser->equippedBadges->keyBy('slot')` to get slot→badge mapping
### Step 12 — profile.blade.php: Achievement gallery
Add below stats grid (only if profile user is student):
- Section title: "ความสำเร็จ" with total count badge
- `grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-3`
- Each: `relative w-16 h-16 rounded-xl` + `filter grayscale opacity-40` if locked
- Tooltip on hover (Alpine.js `x-data`, `x-show`)
- Load all achievements + user's unlocked IDs in Profile.php mount()
- Only render section if `$profileUser->isStudent()`

### Step 13 — Student/Achievements.php: Add security guard
```php
public function mount(): void {
    abort_unless(auth()->user()->isStudent(), 403);
    // existing...
}
```

---

## File Checklist

| File | Action |
|------|--------|
| `database/migrations/YYYY_create_user_equipped_badges_table.php` | CREATE (new migration) |
| `app/Models/UserEquippedBadge.php` | CREATE (new model) |
| `app/Models/User.php` | UPDATE (add equippedBadges relation) |
| `database/seeders/GamificationFeaturesSeeder.php` | UPDATE (deactivate 3, rename 1, add 6, update rewards) |
| `app/Livewire/Auth/Register.php` | UPDATE (call awardForRegistered) |
| `app/Livewire/Classroom/StreamComment.php` | UPDATE (call awardForCommented) |
| `app/Livewire/Assignment/Grade.php` | UPDATE (call awardForPerfectScore) |
| `app/Livewire/Assignment/Show.php` | UPDATE (pass submission+assignment to awardForAssignmentTurnedIn) |
| `app/Livewire/Assignment/Attendance.php` | UPDATE (pass submission+assignment to awardForAssignmentTurnedIn) |
| `app/Livewire/Settings.php` | UPDATE (badge equip logic) |
| `app/Livewire/Profile.php` | UPDATE (load badges + achievements) |
| `resources/js/app.js` | UPDATE (achievement unlock toast listener) |
| `resources/views/livewire/settings.blade.php` | UPDATE (badge equip section) |
| `resources/views/livewire/profile.blade.php` | UPDATE (badges + achievement gallery) |
| `resources/views/livewire/student/achievements.blade.php` | UPDATE (minor — already OK) |
| `app/Livewire/Student/Achievements.php` | UPDATE (add student guard) |

**Total: 17 files** (2 create, 15 update)

---

## Constraints & Notes

- All text is **Thai-only** (no multi-language).
- All gamification rewards go to **students only** (`isEligible()` guard).
- `unlockAchievement()` is idempotent (checks if already unlocked) — safe to call multiple times.
- Toast is dispatched via Livewire browser event — works in any Livewire component without page reload.
- Badge equip uses `user_equipped_badges` table (composite PK `user_id+slot`) — no NULL columns on users, `cascadeOnDelete()` on both FKs.
- Profile achievement gallery shows **all** achievements (even locked) — shows the full picture of what's available.
- Achievement badge images: all use same placeholder SVG for now; can be updated per-achievement via admin panel later.
- `on_a_roll` counts submissions in last 7 days including the current one.
- Perfect score logic counts via JOIN to handle `max_score` per assignment correctly.
