<?php

namespace App\Livewire\Student;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Achievements extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.achievements');
    }
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        $allAchievements = Achievement::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $unlockedAchievementIds = $user->achievements()
            ->pluck('achievements.id')
            ->all();
        $unlockedLookup = array_flip($unlockedAchievementIds);
        $unlockedCount = count($unlockedAchievementIds);
        $totalCount = $allAchievements->count();

        return view('livewire.student.achievements', [
            'allAchievements' => $allAchievements,
            'unlockedAchievementIds' => $unlockedAchievementIds,
            'unlockedLookup' => $unlockedLookup,
            'unlockedCount' => $unlockedCount,
            'totalCount' => $totalCount,
            'completionPercent' => $totalCount > 0 ? (int) round(($unlockedCount / $totalCount) * 100) : 0,
        ]);
    }
}
