<?php

namespace App\Livewire\Student;

use App\Models\UserGamification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Leaderboard extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.leaderboard');
    }
    public function render()
    {
        // Get top 50 students ordered by level (desc) and xp (desc)
        $topStudents = UserGamification::select('user_gamifications.*')
            ->join('users', 'users.id', '=', 'user_gamifications.user_id')
            ->where('users.role', 'student')
            ->with('user')
            ->orderBy('user_gamifications.level', 'desc')
            ->orderBy('user_gamifications.xp', 'desc')
            ->take(50)
            ->get();

        return view('livewire.student.leaderboard', [
            'topStudents' => $topStudents,
        ]);
    }
}
