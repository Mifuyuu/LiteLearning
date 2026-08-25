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

        $windowStart = $now;
        $windowEnd = $now->copy()->addHours(24);

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

                // Dedup: skip if already notified for this assignment+user (ส่งครั้งเดียว)
                $cacheKey = "due_reminder:{$assignment->id}:{$student->id}";
                if (Cache::has($cacheKey)) {
                    continue;
                }

                $remaining = $assignment->due_date->diffForHumans(syntax: Carbon::DIFF_ABSOLUTE);
                $student->notify(new AssignmentDueSoon($assignment, $remaining));

                // บันทึก cache จนเลยกำหนดส่ง + 2 ชม. เพื่อไม่ให้ส่งซ้ำอีก
                Cache::put($cacheKey, true, $assignment->due_date->copy()->addHours(2));
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} due-date reminder emails.");
        }

        return Command::SUCCESS;
    }
}
