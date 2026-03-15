<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\Topic;
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

    public function mount(Classroom $classroom): void
    {
        abort_unless($classroom->canManageClassroom(Auth::user()), 403);

        $this->classroom = $classroom;
    }

    // ──────────────────────────────────────────────
    //  Data helpers
    // ──────────────────────────────────────────────

    private function getAssignments(): Collection
    {
        return $this->classroom->assignments()
            ->with('classworkItem.topic')
            ->published()
            ->when($this->filterType, fn ($q) => $q->ofType($this->filterType))
            ->when($this->filterTopic, fn ($q) => $q->whereHas(
                'classworkItem',
                fn ($q2) => $q2->whereHas(
                    'topic',
                    fn ($q3) => $q3->where('name', $this->filterTopic)
                )
            ))
            ->whereNotIn('assignments.type', ['material', 'announcement', 'topic'])
            ->orderBy('assignments.created_at')
            ->get();
    }

    private function getStudents(): Collection
    {
        return $this->classroom->students()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Build lookup map: [userId][assignmentId] => Submission|null
     */
    private function buildScoreMap(Collection $students, Collection $assignments): array
    {
        $studentIds = $students->pluck('id');

        // Eager-load all relevant submissions in one query
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

    /**
     * Compute per-student summary: total_score, max_possible, avg_percent, graded_count
     */
    private function studentSummary(int $userId, Collection $assignments, array $scoreMap): array
    {
        $totalScore = 0;
        $maxPossible = 0;
        $gradedCount = 0;

        foreach ($assignments as $assignment) {
            $sub = $scoreMap[$userId][$assignment->id] ?? null;
            if ($sub && $sub->isGraded()) {
                $totalScore += $sub->score;
                $maxPossible += $assignment->max_score ?? 0;
                $gradedCount++;
            }
        }

        $avgPercent = ($maxPossible > 0)
            ? round($totalScore / $maxPossible * 100)
            : null;

        return compact('totalScore', 'maxPossible', 'avgPercent', 'gradedCount');
    }

    // ──────────────────────────────────────────────
    //  Stats for the summary cards
    // ──────────────────────────────────────────────

    public function getStatsProperty(): array
    {
        $assignments = $this->getAssignments();
        $students = $this->classroom->students()->get(); // unfiltered for stats
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
            $totalPercent = $graded->sum(function ($sub) use ($assignments) {
                $maxScore = $assignments->firstWhere('id', $sub->assignment_id)?->max_score ?? 0;

                return $maxScore > 0 ? ($sub->score / $maxScore * 100) : 0;
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

    // ──────────────────────────────────────────────
    //  Available filter options
    // ──────────────────────────────────────────────

    public function getTopicsProperty(): Collection
    {
        return Topic::where('classroom_id', $this->classroom->id)
            ->whereHas('classworkItems', function ($q) {
                $q->where('classroom_id', $this->classroom->id)
                    ->where('type', 'assignment');
            })
            ->pluck('name');
    }

    public function getTypesProperty(): array
    {
        return ['file', 'question', 'project', 'attendance'];
    }

    // ──────────────────────────────────────────────
    //  Export CSV
    // ──────────────────────────────────────────────

    public function exportCsv(): StreamedResponse
    {
        $classroom = $this->classroom;
        $assignments = $this->getAssignments();
        $students = $this->getStudents();
        $scoreMap = $this->buildScoreMap($students, $assignments);

        $filename = 'grade_report_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($assignments, $students, $scoreMap) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            $headers = ['ชื่อ', 'อีเมล'];
            foreach ($assignments as $assignment) {
                $headers[] = $assignment->title.' (/'.$assignment->max_score.')';
            }
            $headers[] = 'คะแนนรวม';
            $headers[] = 'เฉลี่ย (%)';
            fputcsv($handle, $headers);

            // Data rows
            foreach ($students as $student) {
                $row = [$student->name, $student->email];
                $summary = $this->studentSummary($student->id, $assignments, $scoreMap);

                foreach ($assignments as $assignment) {
                    $sub = $scoreMap[$student->id][$assignment->id] ?? null;
                    if ($sub && $sub->isGraded()) {
                        $row[] = $sub->score.'/'.$assignment->max_score;
                    } elseif ($sub && $sub->isTurnedIn()) {
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

    // ──────────────────────────────────────────────
    //  Render
    // ──────────────────────────────────────────────

    public function render()
    {
        $assignments = $this->getAssignments();
        $students = $this->getStudents();
        $scoreMap = $this->buildScoreMap($students, $assignments);

        return view('livewire.classroom.grade-report', [
            'assignments' => $assignments,
            'students' => $students,
            'scoreMap' => $scoreMap,
            'stats' => $this->stats,
            'topics' => $this->topics,
            'types' => $this->types,
        ])->title($this->classroom->name.' — '.__('Grade Report'));
    }
}
