<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\CoinTransaction;
use App\Models\User;

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

        $user->increment('coins', $amount);
        $user->refresh();
    }

    public function awardXp(User $user, int $amount): void
    {
        if (!$this->isEligible($user)) {
            return;
        }

        if ($amount <= 0) {
            return;
        }

        $newXp = $user->xp + $amount;
        $newLevel = $this->resolveLevelFromXp($newXp);
        $levelUps = max(0, $newLevel - $user->level);

        $user->update([
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

    public function awardBadge(User $user, string $code): void
    {
        if (!$this->isEligible($user)) {
            return;
        }

        $badge = Badge::where('code', $code)->first();
        if (!$badge) {
            return;
        }

        if ($user->badges()->where('badge_id', $badge->id)->exists()) {
            return;
        }

        $user->badges()->attach($badge->id, [
            'earned_at' => now(),
        ]);
    }

    public function awardForClassroomCreated(User $user, int $classroomId): void
    {
        $this->awardCoins($user, 30, 'classroom_created', 'classroom', $classroomId);
        $this->awardXp($user, 40);

        if ($user->ownedClassrooms()->count() >= 1) {
            $this->unlockAchievement($user, 'first_classroom_created');
            $this->awardBadge($user, 'class-starter');
        }

        if ($user->ownedClassrooms()->count() >= 5) {
            $this->unlockAchievement($user, 'classroom_builder');
            $this->awardBadge($user, 'master-teacher');
        }
    }

    public function awardForClassroomJoined(User $user, int $classroomId): void
    {
        $this->awardCoins($user, 15, 'classroom_joined', 'classroom', $classroomId);
        $this->awardXp($user, 25);

        if ($user->enrolledClassrooms()->count() >= 1) {
            $this->unlockAchievement($user, 'first_classroom_joined');
            $this->awardBadge($user, 'new-learner');
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
            $this->awardBadge($user, 'submission-pro');
        }
    }

    public function resolveLevelFromXp(int $xp): int
    {
        $level = 1;
        while ($xp >= $this->totalXpForLevel($level + 1)) {
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
}
