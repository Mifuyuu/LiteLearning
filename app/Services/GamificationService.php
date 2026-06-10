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

    private function awardEventReward(
        User $user,
        int $coins,
        int $xp,
        string $source,
        string $referenceType,
        int $referenceId
    ): bool {
        if (! $this->isEligible($user)) {
            return false;
        }

        return DB::transaction(function () use ($user, $coins, $xp, $source, $referenceType, $referenceId): bool {
            $alreadyAwarded = CoinTransaction::query()
                ->where('user_id', $user->id)
                ->where('source', $source)
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->exists();

            if ($alreadyAwarded) {
                return false;
            }

            $now = now();
            $inserted = DB::table('coin_transactions')->insertOrIgnore([
                'user_id' => $user->id,
                'amount' => $coins,
                'type' => 'earn',
                'source' => $source,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'idempotency_key' => "{$source}:{$user->id}:{$referenceId}",
                'metadata' => null,
                'happened_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($inserted === 0) {
                return false;
            }

            $gamification = $user->gamification()->firstOrCreate(
                ['user_id' => $user->id],
                ['coins' => 0, 'xp' => 0, 'level' => 1]
            );
            $gamification->increment('coins', $coins);

            $this->awardXp($user, $xp);

            return true;
        });
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
        $levelUps = max(0, $newLevel - $gamification->level);

        $gamification->update([
            'xp' => $newXp,
            'level' => $newLevel,
        ]);

        if ($levelUps > 0) {
            $bonusCoins = $levelUps * 20;
            $this->awardCoins($user, $bonusCoins, 'level_up', null, null, [
                'levels_gained' => $levelUps,
                'new_level' => $newLevel,
            ]);
        }

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

        if ($achievement->coin_reward > 0) {
            $this->awardCoins($user, (int) $achievement->coin_reward, 'achievement', Achievement::class, $achievement->id, [
                'code' => $achievement->code,
            ]);
        }

        if ($achievement->xp_reward > 0) {
            $this->awardXp($user, (int) $achievement->xp_reward);
        }
    }

    public function awardForClassroomCreated(User $user, int $classroomId): void
    {
        $this->awardCoins($user, 30, 'classroom_created', 'classroom', $classroomId);
        $this->awardXp($user, 40);
    }

    public function awardForClassroomJoined(User $user, int $classroomId): void
    {
        if (! $this->awardEventReward($user, 15, 25, 'classroom_joined', 'classroom', $classroomId)) {
            return;
        }

        if ($user->enrolledClassrooms()->count() >= 1) {
            $this->unlockAchievement($user, 'first_classroom_joined');
        }

        if ($user->enrolledClassrooms()->count() >= 3) {
            $this->unlockAchievement($user, 'multi_class');
        }
    }

    public function awardForAssignmentCreated(User $user, int $assignmentId): void
    {
        $this->awardCoins($user, 20, 'assignment_created', 'assignment', $assignmentId);
        $this->awardXp($user, 30);
    }

    public function awardForAssignmentTurnedIn(User $user, int $assignmentId): void
    {
        if (! $this->awardEventReward($user, 10, 20, 'assignment_turned_in', 'assignment', $assignmentId)) {
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

        $gradedCount = $submission->user->submissions()
            ->where('status', 'graded')
            ->count();

        if ($gradedCount >= 5) {
            $this->unlockAchievement($submission->user, 'grade_seeker');
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
        $classroomIds = $user->enrolledClassrooms()->pluck('classrooms.id');

        foreach ($classroomIds as $classroomId) {
            $assignmentIds = Assignment::query()
                ->whereHas('classworkItem', fn ($query) => $query->where('classroom_id', $classroomId))
                ->where('status', 'published')
                ->whereNotIn('type', ['announcement', 'material', 'topic'])
                ->pluck('id');

            if ($assignmentIds->isEmpty()) {
                continue;
            }

            $completedCount = $user->submissions()
                ->whereIn('assignment_id', $assignmentIds)
                ->whereIn('status', ['turned_in', 'graded', 'returned'])
                ->distinct('assignment_id')
                ->count('assignment_id');

            if ($completedCount >= $assignmentIds->count()) {
                return true;
            }
        }

        return false;
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
            throw new GamificationException(__('Only students can purchase items.'));
        }

        if (! $item->is_active) {
            throw new GamificationException(__('This item is no longer available.'));
        }

        if ($user->storeItems()->where('store_item_id', $item->id)->exists()) {
            throw new GamificationException(__('You already own this item.'));
        }

        DB::transaction(function () use ($user, $item) {
            $gamification = $user->gamification()->firstOrCreate(
                ['user_id' => $user->id],
                ['coins' => 0, 'xp' => 0, 'level' => 1]
            );

            if ($user->storeItems()->where('store_item_id', $item->id)->exists()) {
                throw new GamificationException(__('You already own this item.'));
            }

            $coinsDeducted = UserGamification::query()
                ->whereKey($gamification->id)
                ->where('coins', '>=', $item->price)
                ->decrement('coins', $item->price);

            if ($coinsDeducted === 0) {
                throw new GamificationException(__('Not enough coins.'));
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
                throw new GamificationException(__('You already own this item.'), previous: $exception);
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
            throw new GamificationException(__('Only students can equip items.'));
        }

        if (! $user->storeItems()->where('store_item_id', $item->id)->exists()) {
            throw new GamificationException(__('You do not own this item.'));
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
