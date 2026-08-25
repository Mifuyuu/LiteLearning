<?php

namespace App\Services;

use App\Exceptions\GamificationException;
use App\Models\Achievement;
use App\Models\Assignment;
use App\Models\CoinTransaction;
use App\Models\Comment;
use App\Models\StoreItem;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserGamification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    public function __construct(private readonly SettingsService $settings) {}

    private function isEligible(User $user): bool
    {
        return $user->isStudent();
    }

    /**
     * Celebration flashes (level-up modal, achievement toast) live in the
     * current HTTP session, which belongs to whoever is making the request —
     * not necessarily the user being awarded. Without this guard, a teacher
     * grading a submission that levels up the student would see the
     * student's level-up modal pop up on the teacher's own screen.
     */
    private function isCurrentUser(User $user): bool
    {
        return auth()->check() && auth()->id() === $user->id;
    }

    /**
     * Queue a celebration flash for $user under $key ('new_level_ups' or
     * 'new_achievements'). Shown immediately via session flash when $user is
     * making the request themselves; otherwise stashed on their gamification
     * row so it survives until they load a page under their own session.
     */
    private function queueCelebration(User $user, string $key, array $item): void
    {
        if ($this->isCurrentUser($user)) {
            session()->push($key, $item);

            return;
        }

        $gamification = $user->gamification()->firstOrCreate(
            ['user_id' => $user->id],
            ['coins' => 0, 'xp' => 0, 'level' => 1]
        );

        $pending = $gamification->pending_celebrations ?? [];
        $pending[$key][] = $item;
        $gamification->update(['pending_celebrations' => $pending]);
    }

    /**
     * @return bool true if coins were newly awarded, false if this exact event
     *              (same source+user+reference) was already rewarded before —
     *              guards against double-submit/race duplicate calls.
     */
    public function awardCoins(User $user, int $amount, string $source, ?string $referenceType = null, ?int $referenceId = null, array $metadata = []): bool
    {
        if (! $this->isEligible($user)) {
            return false;
        }

        if ($amount === 0) {
            return false;
        }

        $idempotencyKey = $referenceId !== null ? "{$source}:{$user->id}:{$referenceId}" : null;

        try {
            DB::transaction(function () use ($user, $amount, $source, $referenceType, $referenceId, $metadata, $idempotencyKey) {
                CoinTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => $amount > 0 ? 'earn' : 'spend',
                    'source' => $source,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'metadata' => $metadata,
                    'happened_at' => now(),
                    'idempotency_key' => $idempotencyKey,
                ]);

                $gamification = $user->gamification()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['coins' => 0, 'xp' => 0, 'level' => 1]
                );
                $gamification->increment('coins', $amount);
            });
        } catch (QueryException $e) {
            if ($idempotencyKey === null) {
                throw $e;
            }

            // Unique constraint on idempotency_key — this exact event was already rewarded.
            return false;
        }

        return true;
    }

    public function awardXp(User $user, int $amount): void
    {
        if (! $this->isEligible($user)) {
            return;
        }

        if ($amount <= 0) {
            return;
        }

        // Ensure gamification record exists
        $gamification = $user->gamification()->firstOrCreate(
            ['user_id' => $user->id],
            ['coins' => 0, 'xp' => 0, 'level' => 1]
        );

        $oldLevel = $gamification->level;
        $newXp = $gamification->xp + $amount;
        $newLevel = $this->resolveLevelFromXp($newXp);
        $gamification->update([
            'xp' => $newXp,
            'level' => $newLevel,
        ]);

        if ($newLevel > $oldLevel) {
            $this->queueLevelUpCelebration($user, $oldLevel, $newLevel);
        }

        if ($newLevel >= 5) {
            $this->unlockAchievement($user, 'level_up');
        }
    }

    /**
     * Queue one flash entry per level gained so the celebration modal can
     * animate the progress bar filling and the level number ticking up for
     * each one in sequence (a single big XP award can cross several levels).
     */
    private function queueLevelUpCelebration(User $user, int $oldLevel, int $newLevel): void
    {
        for ($level = $oldLevel + 1; $level <= $newLevel; $level++) {
            $this->queueCelebration($user, 'new_level_ups', [
                'level_before' => $level - 1,
                'level_after' => $level,
            ]);
        }
    }

    public function unlockAchievement(User $user, string $code): void
    {
        if (! $this->isEligible($user)) {
            return;
        }

        $achievement = Achievement::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $achievement) {
            return;
        }

        if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
            return;
        }

        try {
            $user->achievements()->attach($achievement->id, [
                'unlocked_at' => now(),
            ]);
        } catch (QueryException $e) {
            // Composite primary key violation — a concurrent request already
            // unlocked this achievement for the user between the exists()
            // check above and this attach(). Already handled by that request.
            return;
        }

        $this->queueCelebration($user, 'new_achievements', [
            'name' => $achievement->name,
            'description' => $achievement->description,
            'badge_image' => $achievement->badge_image,
            'coin_reward' => (int) $achievement->coin_reward,
            'xp_reward' => (int) $achievement->xp_reward,
        ]);

        if ($achievement->coin_reward > 0) {
            $this->awardCoins($user, (int) $achievement->coin_reward, 'achievement', Achievement::class, $achievement->id, [
                'code' => $achievement->code,
            ]);
        }

        if ($achievement->xp_reward > 0) {
            $this->awardXp($user, (int) $achievement->xp_reward);
        }

        if ($code !== 'collector') {
            $this->maybeUnlockCollector($user);
        }
    }

    /**
     * Unlock the 'collector' meta-achievement once every other active
     * achievement has been unlocked.
     */
    private function maybeUnlockCollector(User $user): void
    {
        $requiredIds = Achievement::where('is_active', true)
            ->where('code', '!=', 'collector')
            ->pluck('id');

        if ($requiredIds->isEmpty()) {
            return;
        }

        $unlockedIds = $user->achievements()->pluck('achievements.id');

        if ($requiredIds->diff($unlockedIds)->isEmpty()) {
            $this->unlockAchievement($user, 'collector');
        }
    }

    public function awardForFirstLogin(User $user): void
    {
        $this->unlockAchievement($user, 'explorer');
    }

    public function awardForClassroomJoined(User $user, int $classroomId): void
    {
        if (! $this->isEligible($user)) {
            return;
        }

        if ($user->enrolledClassrooms()->count() >= 1) {
            $this->unlockAchievement($user, 'first_classroom_joined');
        }

        if ($user->enrolledClassrooms()->count() >= 3) {
            $this->unlockAchievement($user, 'multi_class');
        }
    }

    public function awardForAssignmentTurnedIn(User $user, int $assignmentId): void
    {
        if (! $this->isEligible($user)) {
            return;
        }

        $turnedInCount = $user->submissions()->whereIn('status', ['turned_in', 'graded'])->count();
        if ($turnedInCount >= 1) {
            $this->unlockAchievement($user, 'first_assignment_turned_in');
        }

        if ($turnedInCount >= 5) {
            $this->unlockAchievement($user, 'on_a_roll');
        }

        $submission = $user->submissions()
            ->with('assignment.classworkItem')
            ->where('assignment_id', $assignmentId)
            ->first();

        if ($submission instanceof Submission && $this->wasSubmittedAtLeastOneDayEarly($submission)) {
            $this->unlockAchievement($user, 'early_bird');
        }

        if ($this->hasCompletedAnyClassroom($user)) {
            $this->unlockAchievement($user, 'consistent_submitter');
        }
    }

    public function awardForAttendanceCheckin(User $user, int $assignmentId): void
    {
        if (! $this->isEligible($user)) {
            return;
        }

        // Gate XP on the coin award actually happening — awardCoins() returns
        // false when this exact check-in was already rewarded (duplicate/race
        // call), which must also stop the XP from being doubled.
        $coinReward = $this->settings->int('attendance_coin_reward', 50);
        if ($this->awardCoins($user, $coinReward, 'attendance_checkin', Assignment::class, $assignmentId)) {
            $this->awardXp($user, $this->settings->int('attendance_xp_reward', 75));
        }
    }

    public function awardForCommentCreated(User $user, int $commentId): void
    {
        if (! $this->isEligible($user)) {
            return;
        }

        if (! Comment::whereKey($commentId)->where('user_id', $user->id)->exists()) {
            return;
        }

        $commentCount = $user->comments()->count();

        if ($commentCount >= 1) {
            $this->unlockAchievement($user, 'social_butterfly');
        }

        if ($commentCount >= 5) {
            $this->unlockAchievement($user, 'chatterbox');
        }
    }

    public function awardForSubmissionGraded(Submission $submission): void
    {
        $submission->loadMissing('assignment', 'user');

        if (! $submission->user instanceof User || ! $this->isEligible($submission->user)) {
            return;
        }

        $assignment = $submission->assignment;

        if ($assignment instanceof Assignment && $assignment->max_score > 0 && $submission->score === $assignment->max_score) {
            $this->unlockAchievement($submission->user, 'perfect_score');
        }
    }

    private function wasSubmittedAtLeastOneDayEarly(Submission $submission): bool
    {
        $assignment = $submission->assignment;

        if (! $assignment instanceof Assignment || ! $assignment->due_date || ! $submission->turned_in_at) {
            return false;
        }

        return $submission->turned_in_at->lessThanOrEqualTo($assignment->due_date->copy()->subDay());
    }

    private function hasCompletedAnyClassroom(User $user): bool
    {
        // Single query: join the full chain and check if any classroom
        // has all its published assignments completed by this user.
        return DB::table('classroom_user')
            ->where('classroom_user.user_id', $user->id)
            ->join('classrooms', 'classrooms.id', '=', 'classroom_user.classroom_id')
            ->join('classwork_items', 'classwork_items.classroom_id', '=', 'classrooms.id')
            ->join('assignments', function ($join) {
                $join->on('assignments.classwork_item_id', '=', 'classwork_items.id')
                    ->where('assignments.status', 'published')
                    ->whereNotIn('assignments.type', ['announcement', 'material', 'topic']);
            })
            ->leftJoin('submissions', function ($join) use ($user) {
                $join->on('submissions.assignment_id', '=', 'assignments.id')
                    ->where('submissions.user_id', $user->id)
                    ->whereIn('submissions.status', ['turned_in', 'graded']);
            })
            ->groupBy('classrooms.id')
            ->havingRaw('COUNT(assignments.id) > 0')
            ->havingRaw('COUNT(assignments.id) = COUNT(submissions.assignment_id)')
            ->exists();
    }

    /**
     * Resolve a user's level from their total XP.
     * Capped at level 100 to prevent an unbounded loop on very high XP values. (fix #10)
     */
    public function resolveLevelFromXp(int $xp): int
    {
        $maxLevel = 100;
        $level = 1;

        while ($level < $maxLevel && $xp >= $this->totalXpForLevel($level + 1)) {
            $level++;
        }

        return $level;
    }

    public function totalXpForLevel(int $level): int
    {
        if ($level <= 1) {
            return 0;
        }

        $multiplier = $this->settings->int('xp_per_level_multiplier', 100);

        return (int) (((($level - 1) * $level) / 2) * $multiplier);
    }

    /**
     * Admin override: set a student's coins and XP to exact values.
     *
     * @throws GamificationException
     */
    public function adminSetCoinsAndXp(User $user, int $coins, int $xp): void
    {
        if (! $this->isEligible($user)) {
            throw new GamificationException(__('app.store_student_only'));
        }

        DB::transaction(function () use ($user, $coins, $xp) {
            $gamification = $user->gamification()->firstOrCreate(
                ['user_id' => $user->id],
                ['coins' => 0, 'xp' => 0, 'level' => 1]
            );

            $coinDelta = $coins - $gamification->coins;

            $gamification->update([
                'coins' => $coins,
                'xp' => $xp,
                'level' => $this->resolveLevelFromXp($xp),
            ]);

            if ($coinDelta !== 0) {
                CoinTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $coinDelta,
                    'type' => $coinDelta > 0 ? 'earn' : 'spend',
                    'source' => 'admin_adjustment',
                    'happened_at' => now(),
                ]);
            }
        });
    }

    /**
     * Purchase a store item for a user.
     *
     * @throws GamificationException (fix #9)
     */
    public function purchaseItem(User $user, StoreItem $item): void
    {
        if (! $this->settings->bool('store_enabled', true)) {
            throw new GamificationException(__('app.store_disabled'));
        }

        if (! $this->isEligible($user)) {
            throw new GamificationException(__('app.store_student_only'));
        }

        if (! $item->is_active) {
            throw new GamificationException(__('app.store_item_unavailable'));
        }

        DB::transaction(function () use ($user, $item) {
            $gamification = $user->gamification()->firstOrCreate(
                ['user_id' => $user->id],
                ['coins' => 0, 'xp' => 0, 'level' => 1]
            );

            if ($user->storeItems()->where('store_item_id', $item->id)->exists()) {
                throw new GamificationException(__('app.store_already_owned'));
            }

            $coinsDeducted = UserGamification::query()
                ->whereKey($gamification->id)
                ->where('coins', '>=', $item->price)
                ->decrement('coins', $item->price);

            if ($coinsDeducted === 0) {
                throw new GamificationException(__('app.store_insufficient_coins'));
            }

            // Record transaction
            CoinTransaction::create([
                'user_id' => $user->id,
                'amount' => -$item->price,
                'type' => 'spend',
                'source' => 'store_purchase',
                'reference_type' => StoreItem::class,
                'reference_id' => $item->id,
                'happened_at' => now(),
            ]);

            try {
                $user->storeItems()->attach($item->id, ['is_active' => false]);
            } catch (QueryException $exception) {
                throw new GamificationException(__('app.store_already_owned'), previous: $exception);
            }
        });
    }

    /**
     * Equip a purchased store item.
     *
     * @throws GamificationException (fix #9)
     */
    public function equipItem(User $user, StoreItem $item): void
    {
        if (! $this->isEligible($user)) {
            throw new GamificationException(__('app.store_equip_student_only'));
        }

        if (! $user->storeItems()->where('store_item_id', $item->id)->exists()) {
            throw new GamificationException(__('app.store_not_owned'));
        }

        DB::transaction(function () use ($user, $item) {
            // Lock the user row as a mutex so concurrent equip() calls for this
            // user serialize — otherwise two simultaneous equips of the same
            // type could both read the old active item as still-active and
            // leave two items active at once.
            User::whereKey($user->id)->lockForUpdate()->first();

            $user->storeItems()
                ->wherePivot('is_active', true)
                ->where('type', $item->type)
                ->each(fn ($owned) => $user->storeItems()->updateExistingPivot($owned->id, ['is_active' => false]));

            $user->storeItems()->updateExistingPivot($item->id, ['is_active' => true]);
        });
    }

    /**
     * Toggle whether an unlocked achievement is shown as a badge after the user's name.
     * Capped at User::MAX_DISPLAYED_BADGES simultaneously displayed badges.
     *
     * @throws GamificationException
     */
    public function toggleAchievementDisplay(User $user, Achievement $achievement): void
    {
        DB::transaction(function () use ($user, $achievement): void {
            // Lock the user row as a mutex so concurrent toggle calls for this
            // user serialize — otherwise two simultaneous "turn on" calls could
            // both pass the cap check before either commits.
            User::whereKey($user->id)->lockForUpdate()->first();

            $unlocked = $user->achievements()->where('achievement_id', $achievement->id)->first();

            if (! $unlocked) {
                throw new GamificationException(__('app.badge_not_unlocked'));
            }

            if ($unlocked->pivot->is_displayed) {
                $user->achievements()->updateExistingPivot($achievement->id, ['is_displayed' => false]);

                return;
            }

            if ($user->achievements()->wherePivot('is_displayed', true)->count() >= User::MAX_DISPLAYED_BADGES) {
                throw new GamificationException(__('app.badge_display_limit', ['max' => User::MAX_DISPLAYED_BADGES]));
            }

            $user->achievements()->updateExistingPivot($achievement->id, ['is_displayed' => true]);
        });
    }
}
