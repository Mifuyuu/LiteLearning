<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class GradeReport extends Component
{
    public Classroom $classroom;

    #[Url(except: '')]
    public string $filterTopic = '';

    #[Url(except: '')]
    public string $filterType = '';

    #[Url(except: '')]
    public string $search = '';

    public string $sort = 'sort-last-name';

    public string $display = 'default';

    public function mount(Classroom $classroom, string $sort = 'sort-last-name', string $display = 'default'): void
    {
        abort_unless($classroom->canManageClassroom(Auth::user()), 403);

        $this->classroom = $classroom;
        $this->sort = $this->normalizeSort($sort);
        $this->display = $this->normalizeDisplay($display);
    }

    private function normalizeSort(string $sort): string
    {
        return in_array($sort, ['sort-first-name', 'sort-last-name', 'sort-newest'], true)
            ? $sort
            : 'sort-last-name';
    }

    private function normalizeDisplay(string $display): string
    {
        return in_array($display, ['default', 'compact'], true) ? $display : 'default';
    }

    private function lastNameKey(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name)) ?: [$name];

        return mb_strtolower((string) end($parts));
    }

    private function sortUsers(Collection $users): Collection
    {
        $sorted = match ($this->sort) {
            'sort-newest' => $users->sortByDesc(fn (User $user) => $user->pivot?->joined_at ?? $user->created_at),
            'sort-first-name' => $users->sortBy(fn (User $user) => mb_strtolower($user->name)),
            default => $users->sortBy(fn (User $user) => $this->lastNameKey($user->name).'|'.mb_strtolower($user->name)),
        };

        return $sorted->values();
    }

    private function getAssignments(): Collection
    {
        return $this->classroom->assignments()
            ->with('classworkItem.topic')
            ->published()
            ->when($this->filterType, fn ($query) => $query->ofType($this->filterType))
            ->when($this->filterTopic, fn ($query) => $query->whereHas(
                'classworkItem',
                fn ($classworkQuery) => $classworkQuery->whereHas(
                    'topic',
                    fn ($topicQuery) => $topicQuery->where('name', $this->filterTopic)
                )
            ))
            ->whereNotIn('assignments.type', ['material', 'announcement', 'topic'])
            ->orderBy('assignments.created_at')
            ->get();
    }

    private function getStudents(): Collection
    {
        $students = $this->classroom->students()
            ->when($this->search, function ($query) {
                $query->where(function ($nested): void {
                    $nested->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->get();

        return $this->sortUsers($students);
    }

    private function buildScoreMap(Collection $students, Collection $assignments): array
    {
        $studentIds = $students->pluck('id');

        $submissions = \App\Models\Submission::query()
            ->whereIn('user_id', $studentIds)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $map = [];
        foreach ($students as $student) {
            $map[$student->id] = [];
            $studentSubs = $submissions->get($student->id, collect())->keyBy('assignment_id');
            foreach ($assignments as $assignment) {
                $map[$student->id][$assignment->id] = $studentSubs->get($assignment->id);
            }
        }

        return $map;
    }

    private function studentSummary(int $userId, Collection $assignments, array $scoreMap): array
    {
        $totalScore = 0;
        $maxPossible = 0;
        $gradedCount = 0;

        foreach ($assignments as $assignment) {
            $submission = $scoreMap[$userId][$assignment->id] ?? null;
            if ($submission && $submission->isGraded()) {
                $totalScore += $submission->score;
                $maxPossible += $assignment->max_score ?? 0;
                $gradedCount++;
            }
        }

        $avgPercent = $maxPossible > 0
            ? round($totalScore / $maxPossible * 100)
            : null;

        return compact('totalScore', 'maxPossible', 'avgPercent', 'gradedCount');
    }

    public function getStatsProperty(): array
    {
        $assignments = $this->getAssignments();
        $students = $this->classroom->students()->get();
        $totalSlots = $assignments->count() * $students->count();

        if ($totalSlots === 0) {
            return [
                'assignment_count' => 0,
                'submission_rate' => 0,
                'avg_score' => null,
                'pending_grading' => 0,
            ];
        }

        $submissions = \App\Models\Submission::query()
            ->whereIn('user_id', $students->pluck('id'))
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->get();

        $turnedIn = $submissions->whereIn('status', ['turned_in', 'graded', 'returned'])->count();
        $graded = $submissions->where('status', 'graded');
        $pendingGrading = $submissions->where('status', 'turned_in')->count();

        $avgScore = null;
        if ($graded->count() > 0) {
            $totalPercent = $graded->sum(function ($submission) use ($assignments) {
                $maxScore = $assignments->firstWhere('id', $submission->assignment_id)?->max_score ?? 0;

                return $maxScore > 0 ? ($submission->score / $maxScore * 100) : 0;
            });
            $avgScore = round($totalPercent / $graded->count());
        }

        return [
            'assignment_count' => $assignments->count(),
            'submission_rate' => $totalSlots > 0 ? round($turnedIn / $totalSlots * 100) : 0,
            'avg_score' => $avgScore,
            'pending_grading' => $pendingGrading,
        ];
    }

    public function getTopicsProperty(): Collection
    {
        return Topic::where('classroom_id', $this->classroom->id)
            ->whereHas('classworkItems', function ($query) {
                $query->where('classroom_id', $this->classroom->id)
                    ->where('type', 'assignment');
            })
            ->pluck('name');
    }

    public function getTypesProperty(): array
    {
        return ['file', 'question', 'project', 'attendance'];
    }

    public function exportCsv(): StreamedResponse
    {
        $assignments = $this->getAssignments();
        $students = $this->getStudents();
        $scoreMap = $this->buildScoreMap($students, $assignments);

        $filename = 'grade_report_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($assignments, $students, $scoreMap) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            $headers = ['ชื่อ', 'อีเมล'];
            foreach ($assignments as $assignment) {
                $headers[] = $assignment->title.' (/'.$assignment->max_score.')';
            }
            $headers[] = 'คะแนนรวม';
            $headers[] = 'เฉลี่ย (%)';
            fputcsv($handle, $headers);

            foreach ($students as $student) {
                $row = [$student->name, $student->email];
                $summary = $this->studentSummary($student->id, $assignments, $scoreMap);

                foreach ($assignments as $assignment) {
                    $submission = $scoreMap[$student->id][$assignment->id] ?? null;
                    if ($submission && $submission->isGraded()) {
                        $row[] = $submission->score.'/'.$assignment->max_score;
                    } elseif ($submission && $submission->isTurnedIn()) {
                        $row[] = 'รอให้คะแนน';
                    } else {
                        $row[] = '-';
                    }
                }

                $row[] = $summary['maxPossible'] > 0 ? $summary['totalScore'].'/'.$summary['maxPossible'] : '-';
                $row[] = $summary['avgPercent'] !== null ? $summary['avgPercent'].'%' : '-';
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $assignments = $this->getAssignments();
        $students = $this->getStudents();
        $scoreMap = $this->buildScoreMap($students, $assignments);
        $summaries = [];

        foreach ($students as $student) {
            $summaries[$student->id] = $this->studentSummary($student->id, $assignments, $scoreMap);
        }

        return view('livewire.classroom.grade-report', [
            'assignments' => $assignments,
            'students' => $students,
            'scoreMap' => $scoreMap,
            'summaries' => $summaries,
            'stats' => $this->stats,
            'topics' => $this->topics,
            'types' => $this->types,
        ])->title($this->classroom->name.' - '.__('Gradebook'));
    }
}
