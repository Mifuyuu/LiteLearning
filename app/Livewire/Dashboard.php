<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $classrooms = $user->allClassrooms();

        // Get upcoming assignments
        $upcomingAssignments = collect();
        foreach ($classrooms as $classroom) {
            $assignments = $classroom->assignments()
                ->published()
                ->where('type', '!=', 'material')
                ->where('due_date', '>=', now())
                ->orderBy('due_date')
                ->take(5)
                ->get();
            $upcomingAssignments = $upcomingAssignments->merge($assignments);
        }
        $upcomingAssignments = $upcomingAssignments->sortBy('due_date')->take(10);

        // Stats for teachers
        $stats = [];
        if ($user->isTeacher() || $user->isAdmin()) {
            $ownedClassrooms = $user->ownedClassrooms()->where('is_archived', false)->get();
            $totalStudents = 0;
            $totalAssignments = 0;
            $pendingSubmissions = 0;

            foreach ($ownedClassrooms as $c) {
                $totalStudents += $c->students()->count();
                $totalAssignments += $c->assignments()->count();
                foreach ($c->assignments as $a) {
                    $pendingSubmissions += $a->submissions()->where('status', 'turned_in')->count();
                }
            }

            $stats = [
                'classrooms' => $ownedClassrooms->count(),
                'students' => $totalStudents,
                'assignments' => $totalAssignments,
                'pending' => $pendingSubmissions,
            ];
        }

        $gamification = null;
        if ($user->isStudent()) {
            $gamificationService = app(GamificationService::class);
            $currentLevelStartXp = $gamificationService->totalXpForLevel($user->level);
            $nextLevelXp = $gamificationService->totalXpForLevel($user->level + 1);
            $xpInCurrentLevel = max(0, $user->xp - $currentLevelStartXp);
            $xpNeededInLevel = max(1, $nextLevelXp - $currentLevelStartXp);

            $gamification = [
                'coins' => $user->coins,
                'level' => $user->level,
                'xp' => $user->xp,
                'achievements' => $user->achievements()->count(),
                'badges' => $user->badges()->count(),
                'xp_to_next' => max(0, $nextLevelXp - $user->xp),
                'progress_percent' => (int) min(100, round(($xpInCurrentLevel / $xpNeededInLevel) * 100)),
            ];
        }

        return view('livewire.dashboard', [
            'classrooms' => $classrooms,
            'upcomingAssignments' => $upcomingAssignments,
            'stats' => $stats,
            'gamification' => $gamification,
        ]);
    }
}
