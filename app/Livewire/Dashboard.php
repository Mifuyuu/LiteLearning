<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    private function buildStudentAnalytics(User $user, Collection $classrooms): array
    {
        $dates = collect(range(0, 6))
            ->map(fn (int $offset) => now()->copy()->startOfDay()->subDays(6 - $offset));

        $emptyChart = [
            'labels' => $dates->map(fn ($date) => $date->translatedFormat('j M'))->all(),
            'submissions' => array_fill(0, 7, 0),
        ];

        $classroomIds = $classrooms->pluck('id')->filter();

        if ($classroomIds->isEmpty()) {
            return [
                'assignment_status' => [
                    'turned_in' => 0,
                    'graded' => 0,
                    'returned' => 0,
                    'missing' => 0,
                ],
                'totals' => [
                    'assignments' => 0,
                    'pending_review' => 0,
                    'average_score' => 0,
                ],
                'charts' => [
                    'assignment_status' => [
                        'labels' => ['รอตรวจ', 'ให้คะแนนแล้ว', 'ส่งกลับมาแก้', 'ยังไม่ส่ง'],
                        'data' => [0, 0, 0, 0],
                    ],
                    'classroom_progress' => [
                        'labels' => [],
                        'completed' => [],
                        'remaining' => [],
                        'colors' => [],
                    ],
                    'recent_activity' => $emptyChart,
                ],
            ];
        }

        $assignments = Assignment::query()
            ->with([
                'classroom.themeCategory',
                'submissions' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->whereIn('classroom_id', $classroomIds)
            ->published()
            ->whereNotIn('type', ['material', 'announcement', 'topic'])
            ->orderBy('due_date')
            ->get();

        $statusCounts = [
            'turned_in' => 0,
            'graded' => 0,
            'returned' => 0,
            'missing' => 0,
        ];

        foreach ($assignments as $assignment) {
            $submission = $assignment->submissions->first();

            if (! $submission) {
                $statusCounts['missing']++;
                continue;
            }

            if (array_key_exists($submission->status, $statusCounts)) {
                $statusCounts[$submission->status]++;
                continue;
            }

            $statusCounts['turned_in']++;
        }

        $scoredSubmissions = $assignments
            ->map(fn (Assignment $assignment) => $assignment->submissions->first())
            ->filter(fn ($submission) => $submission !== null && $submission->score !== null);

        $classroomProgress = $assignments
            ->groupBy('classroom_id')
            ->map(function (Collection $items): array {
                $firstAssignment = $items->first();

                $completed = $items->filter(function (Assignment $assignment): bool {
                    $submission = $assignment->submissions->first();

                    return $submission !== null && in_array($submission->status, ['turned_in', 'graded', 'returned'], true);
                })->count();

                return [
                    'name' => $firstAssignment->classroom?->name ?? 'ห้องเรียน',
                    'completed' => $completed,
                    'remaining' => max(0, $items->count() - $completed),
                    'color' => $firstAssignment->classroom?->themeCategory?->color ?? '#8B5CF6',
                ];
            })
            ->sortByDesc(fn (array $item): int => $item['completed'] + $item['remaining'])
            ->take(6)
            ->values();

        $submissionsByDay = $user->submissions()
            ->whereNotNull('turned_in_at')
            ->whereDate('turned_in_at', '>=', now()->copy()->subDays(6)->toDateString())
            ->get()
            ->groupBy(fn (Submission $submission): string => $submission->turned_in_at?->toDateString() ?? '');

        $recentActivity = [
            'labels' => $dates->map(fn ($date) => $date->translatedFormat('j M'))->all(),
            'submissions' => $dates->map(function ($date) use ($submissionsByDay): int {
                return $submissionsByDay->get($date->toDateString(), collect())->count();
            })->all(),
        ];

        return [
            'assignment_status' => $statusCounts,
            'totals' => [
                'assignments' => $assignments->count(),
                'pending_review' => $statusCounts['turned_in'],
                'average_score' => round((float) ($scoredSubmissions->avg('score') ?? 0), 1),
            ],
            'charts' => [
                'assignment_status' => [
                    'labels' => ['รอตรวจ', 'ให้คะแนนแล้ว', 'ส่งกลับมาแก้', 'ยังไม่ส่ง'],
                    'data' => [
                        $statusCounts['turned_in'],
                        $statusCounts['graded'],
                        $statusCounts['returned'],
                        $statusCounts['missing'],
                    ],
                ],
                'classroom_progress' => [
                    'labels' => $classroomProgress->pluck('name')->all(),
                    'completed' => $classroomProgress->pluck('completed')->all(),
                    'remaining' => $classroomProgress->pluck('remaining')->all(),
                    'colors' => $classroomProgress->pluck('color')->all(),
                ],
                'recent_activity' => $recentActivity,
            ],
        ];
    }

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

            $totalStudents = $ownedClassrooms->sum('students_count');
            $totalAssignments = $ownedClassrooms->sum('assignments_count');

            // Single aggregate query via direct classroom_id
            $assignmentIds = \App\Models\Assignment::whereIn('classroom_id', $ownedClassrooms->pluck('id'))
                ->pluck('id');

            $pendingSubmissions = Submission::whereIn('assignment_id', $assignmentIds)
                ->where('status', 'turned_in')->count();

            $stats = [
                'classrooms' => $ownedClassrooms->count(),
                'students' => $totalStudents,
                'assignments' => $totalAssignments,
                'pending' => $pendingSubmissions,
            ];
        }

        $gamification = null;
        $studentAnalytics = null;
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
                'xp_to_next' => max(0, $nextLevelXp - $user->xp),
                'progress_percent' => (int) min(100, round(($xpInCurrentLevel / $xpNeededInLevel) * 100)),
            ];

            $studentAnalytics = $this->buildStudentAnalytics($user, $classrooms);
        }

        return view('livewire.dashboard', [
            'classrooms' => $classrooms,
            'upcomingAssignments' => $upcomingAssignments,
            'stats' => $stats,
            'gamification' => $gamification,
            'studentAnalytics' => $studentAnalytics,
        ]);
    }
}
