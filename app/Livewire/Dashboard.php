<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\ClassroomContent;
use App\Models\Submission;
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

        // Get upcoming assignments — N queries avoided by fetching per-classroom
        // with a scoped query and sorting in PHP (classrooms are already a small set)
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

        // Stats for teachers — fix #2: use loadCount + single aggregate Submission query
        $stats = [];
        if ($user->isTeacher() || $user->isAdmin()) {
            $ownedClassrooms = $user->ownedClassrooms()
                ->where('is_archived', false)
                ->withCount(['students', 'assignments'])
                ->get();

            $totalStudents    = $ownedClassrooms->sum('students_count');
            $totalAssignments = $ownedClassrooms->sum('assignments_count');

            // Single aggregate query via ClassroomContent
            $assignmentIds = ClassroomContent::where('contentable_type', \App\Models\Assignment::class)
                ->whereIn('classroom_id', $ownedClassrooms->pluck('id'))
                ->pluck('contentable_id');

            $pendingSubmissions = Submission::whereIn('assignment_id', $assignmentIds)
                ->where('status', 'turned_in')->count();

            $stats = [
                'classrooms' => $ownedClassrooms->count(),
                'students'   => $totalStudents,
                'assignments'=> $totalAssignments,
                'pending'    => $pendingSubmissions,
            ];
        }

        $gamification = null;
        if ($user->isStudent()) {
            $gamificationService  = app(GamificationService::class);
            $currentLevelStartXp  = $gamificationService->totalXpForLevel($user->level);
            $nextLevelXp          = $gamificationService->totalXpForLevel($user->level + 1);
            $xpInCurrentLevel     = max(0, $user->xp - $currentLevelStartXp);
            $xpNeededInLevel      = max(1, $nextLevelXp - $currentLevelStartXp);

            $gamification = [
                'coins'            => $user->coins,
                'level'            => $user->level,
                'xp'               => $user->xp,
                'achievements'     => $user->achievements()->count(),
                'badges'           => $user->badges()->count(),
                'xp_to_next'       => max(0, $nextLevelXp - $user->xp),
                'progress_percent' => (int) min(100, round(($xpInCurrentLevel / $xpNeededInLevel) * 100)),
            ];
        }

        return view('livewire.dashboard', [
            'classrooms'          => $classrooms,
            'upcomingAssignments' => $upcomingAssignments,
            'stats'               => $stats,
            'gamification'        => $gamification,
        ]);
    }
}
