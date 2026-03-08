<?php

namespace App\Livewire\Student;

use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Achievements extends Component
{
    public function render()
    {
        $user = Auth::user();
        
        $allAchievements = Achievement::where('is_active', true)->get();
        $unlockedAchievementIds = $user->achievements()->pluck('achievements.id')->toArray();
        
        return view('livewire.student.achievements', [
            'allAchievements' => $allAchievements,
            'unlockedAchievementIds' => $unlockedAchievementIds,
        ]);
    }
}
