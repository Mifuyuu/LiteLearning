<?php

namespace App\Livewire\Student;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Collection;
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

    private Collection $allAchievements;

    private Collection $unlockedAchievements;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->allAchievements = Achievement::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $this->unlockedAchievements = $user->achievements()
            ->get()
            ->keyBy('id');
    }

    public function render()
    {
        $unlockedCount = $this->unlockedAchievements->count();
        $totalCount = $this->allAchievements->count();

        return view('livewire.student.achievements', [
            'allAchievements' => $this->allAchievements,
            'unlockedLookup' => $this->unlockedAchievements,
            'unlockedCount' => $unlockedCount,
            'totalCount' => $totalCount,
            'completionPercent' => $totalCount > 0
                ? (int) round(($unlockedCount / $totalCount) * 100) : 0,
        ]);
    }
}
