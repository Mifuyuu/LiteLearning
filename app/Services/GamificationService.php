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
    private function isEligible(User $user): bool
    {
        return $user->isStudent();
    }

    public function awardCoins(User $user, int $amount, string $source, ?string $referenceType = null, ?int $referenceId = null, array $metadata = []): void
    {
        if (! $this->isEligible($user)) {
            return;
        }

        if ($amount === 0) {
            return;
        }

        DB::transaction(function () use ($user, $amount, $source, $referenceType, $referenceId, $metadata) {
            CoinTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $amount > 0 ? 'earn' : 'spend',
                'source' => $source,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'metadata' => $metadata,
                'happened_at' => now(),
            ]);

            $gamification = $user->gamification()->firstOrCreate(
                ['user_id' => $user->id],
                ['coins' => 0, 'xp' => 0, 'level' => 1]
            );
            $gamification->increment('coins', $amount);
        });
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

        $newXp = $gamification->xp + $amount;
        $newLevel = $this->resolveLevelFromXp($newXp);
        $gamification->update([
            'xp' => $newXp,
            'level' => $newLevel,
        ]);

        if ($newLevel >= 5) {
            $this->unlockAchievement($user, 'level_up');
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

        $user->achievements()->attach($achievement->id, [
            'unlocked_at' => now(),
        ]);

        // ponytail: session flash, existing toast shows it on next render
        session()->push('new_achievements', [
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

        $turnedInCount = $user->submissions()->whereIn('status', ['turned_in', 'graded', 'returned'])->count();
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
                    ->whereIn('submissions.status', ['turned_in', 'graded', 'returned']);
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

        return (int) (((($level - 1) * $level) / 2) * 100);
    }

    /**
     * Purchase a store item for a user.
     *
     * @throws GamificationException (fix #9)
     */
    public function purchaseItem(User $user, StoreItem $item): void
    {
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
                $user->storeItems()->attach($item->id);
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
            $user->storeItems()
                ->wherePivot('is_active', true)
                ->where('type', $item->type)
                ->each(fn ($owned) => $user->storeItems()->updateExistingPivot($owned->id, ['is_active' => false]));

            $user->storeItems()->updateExistingPivot($item->id, ['is_active' => true]);
        });
    }
}
