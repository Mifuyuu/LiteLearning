<?php

namespace App\Livewire\Student;

use App\Models\UserGamification;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Leaderboard extends Component
{
    public function render()
    {
        // Get top 50 students ordered by level (desc) and xp (desc)
        $topStudents = UserGamification::with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'student');
            })
            ->orderBy('level', 'desc')
            ->orderBy('xp', 'desc')
            ->take(50)
            ->get();

        return view('livewire.student.leaderboard', [
            'topStudents' => $topStudents,
        ]);
    }
}
