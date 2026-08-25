<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Submission;
use App\Notifications\AssignmentDueSoon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendDueDateReminders extends Command
{
    protected $signature = 'assignment:send-due-reminders';

    protected $description = 'Send email reminders for assignments due within 24 hours';

    public function handle(): int
    {
        $now = Carbon::now();
        $sent = 0;

        // Window A: due in 6–24 hours (normal reminder)
        // Window B: due in 1–6 hours (tight deadline — catches short deadlines too)
        $windows = [
            ['start' => 6, 'end' => 24, 'label' => 'early'],
            ['start' => 1, 'end' => 6, 'label' => 'urgent'],
        ];

        foreach ($windows as $win) {
            $windowStart = $now->copy()->addHours($win['start']);
            $windowEnd = $now->copy()->addHours($win['end']);

            $dueAssignments = Assignment::query()
                ->where('status', 'published')
                ->whereNotNull('due_date')
                ->where('due_date', '>=', $windowStart)
                ->where('due_date', '<=', $windowEnd)
                ->where('type', '!=', 'attendance')
                ->whereHas('classworkItem.classroom.students')
                ->with(['classworkItem.classroom.students'])
                ->get();

            foreach ($dueAssignments as $assignment) {
                $classroom = $assignment->classworkItem->classroom;
                $students = $classroom->students ?? collect();

                $submittedUserIds = Submission::where('assignment_id', $assignment->id)
                    ->whereIn('status', ['turned_in', 'graded'])
                    ->pluck('user_id')
                    ->all();

                foreach ($students as $student) {
                    if (in_array($student->id, $submittedUserIds, true)) {
                        continue;
                    }

                    // Dedup: skip if already notified for this assignment+user
                    $cacheKey = "due_reminder:{$assignment->id}:{$student->id}";
                    if (Cache::has($cacheKey)) {
                        continue;
                    }

                    $remaining = $assignment->due_date->diffForHumans(now(), ['syntax' => Carbon::DIFF_ABSOLUTE]);
                    $student->notify(new AssignmentDueSoon($assignment, $remaining));

                    // Track sent — TTL จนเลยกำหนดส่ง + 1 ชม. (กันส่งซ้ำถ้า scheduler ติดขัด)
                    Cache::put($cacheKey, true, $assignment->due_date->addHour());
                    $sent++;
                }
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} due-date reminder emails.");
        }

        return Command::SUCCESS;
    }
}
