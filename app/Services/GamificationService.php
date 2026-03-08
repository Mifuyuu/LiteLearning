<?php

namespace App\Services;

use App\Exceptions\GamificationException;
use App\Models\Achievement;
use App\Models\CoinTransaction;
use App\Models\StoreItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    private function isEligible(User $user): bool
    {
        return $user->isStudent();
    }

    public function awardCoins(User $user, int $amount, string $source, ?string $referenceType = null, ?int $referenceId = null, array $metadata = []): void
    {
        if (!$this->isEligible($user)) {
            return;
        }

        if ($amount === 0) {
            return;
        }

        DB::transaction(function () use ($user, $amount, $source, $referenceType, $referenceId, $metadata) {
            CoinTransaction::create([
                'user_id'        => $user->id,
                'amount'         => $amount,
                'type'           => $amount > 0 ? 'earn' : 'spend',
                'source'         => $source,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'metadata'       => $metadata,
                'happened_at'    => now(),
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
        if (!$this->isEligible($user)) {
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

        $newXp    = $gamification->xp + $amount;
        $newLevel = $this->resolveLevelFromXp($newXp);
        $levelUps = max(0, $newLevel - $gamification->level);

        $gamification->update([
            'xp'    => $newXp,
            'level' => $newLevel,
        ]);

        if ($levelUps > 0) {
            $bonusCoins = $levelUps * 20;
            $this->awardCoins($user, $bonusCoins, 'level_up', null, null, [
                'levels_gained' => $levelUps,
                'new_level'     => $newLevel,
            ]);
        }
    }

    public function unlockAchievement(User $user, string $code): void
    {
        if (!$this->isEligible($user)) {
            return;
        }

        $achievement = Achievement::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$achievement) {
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

        $count = $user->ownedClassrooms()->count();

        if ($count >= 1) {
            $this->unlockAchievement($user, 'first_classroom_created');
        }

        if ($count >= 5) {
            $this->unlockAchievement($user, 'classroom_builder');
        }
    }

    public function awardForClassroomJoined(User $user, int $classroomId): void
    {
        $this->awardCoins($user, 15, 'classroom_joined', 'classroom', $classroomId);
        $this->awardXp($user, 25);

        if ($user->enrolledClassrooms()->count() >= 1) {
            $this->unlockAchievement($user, 'first_classroom_joined');
        }
    }

    public function awardForAssignmentCreated(User $user, int $assignmentId): void
    {
        $this->awardCoins($user, 20, 'assignment_created', 'assignment', $assignmentId);
        $this->awardXp($user, 30);

        $createdAssignments = $user->assignments()->count();
        if ($createdAssignments >= 1) {
            $this->unlockAchievement($user, 'first_assignment_created');
        }
    }

    public function awardForAssignmentTurnedIn(User $user, int $assignmentId): void
    {
        $this->awardCoins($user, 10, 'assignment_turned_in', 'assignment', $assignmentId);
        $this->awardXp($user, 20);

        $turnedInCount = $user->submissions()->whereIn('status', ['turned_in', 'graded', 'returned'])->count();
        if ($turnedInCount >= 1) {
            $this->unlockAchievement($user, 'first_assignment_turned_in');
        }

        if ($turnedInCount >= 10) {
            $this->unlockAchievement($user, 'consistent_submitter');
        }
    }

    /**
     * Resolve a user's level from their total XP.
     * Capped at level 100 to prevent an unbounded loop on very high XP values. (fix #10)
     */
    public function resolveLevelFromXp(int $xp): int
    {
        $maxLevel = 100;
        $level    = 1;

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
        if (!$this->isEligible($user)) {
            throw new GamificationException(__('Only students can purchase items.'));
        }

        if (!$item->is_active) {
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

            if ($gamification->coins < $item->price) {
                throw new GamificationException(__('Not enough coins.'));
            }

            // Deduct coins
            $gamification->decrement('coins', $item->price);

            // Record transaction
            CoinTransaction::create([
                'user_id'        => $user->id,
                'amount'         => -$item->price,
                'type'           => 'spend',
                'source'         => 'store_purchase',
                'reference_type' => StoreItem::class,
                'reference_id'   => $item->id,
                'happened_at'    => now(),
            ]);

            // Attach item
            $user->storeItems()->attach($item->id);
        });
    }

    /**
     * Equip a purchased store item.
     *
     * @throws GamificationException (fix #9)
     */
    public function equipItem(User $user, StoreItem $item): void
    {
        if (!$this->isEligible($user)) {
            throw new GamificationException(__('Only students can equip items.'));
        }

        if (!$user->storeItems()->where('store_item_id', $item->id)->exists()) {
            throw new GamificationException(__('You do not own this item.'));
        }

        if ($item->type === 'name_color') {
            $user->update(['active_name_color' => $item->value]);
        } elseif ($item->type === 'avatar_frame') {
            $user->update(['active_avatar_frame' => $item->value]);
        }
    }
}
