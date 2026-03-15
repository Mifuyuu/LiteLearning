<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishScheduledClasswork extends Command
{
    protected $signature = 'classwork:publish-scheduled';

    protected $description = 'Publish classwork items whose scheduled publish time has arrived';

    public function handle(): int
    {
        $due = Assignment::where('status', 'scheduled')
            ->whereHas('classworkItem', function ($q) {
                $q->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
            })
            ->with(['classworkItem.classroom.students'])
            ->get();

        foreach ($due as $assignment) {
            DB::transaction(function () use ($assignment): void {
                $assignment->update(['status' => 'published']);

                $existingStudentIds = $assignment->submissions()->pluck('user_id')->all();
                $students = $assignment->classworkItem->classroom->students ?? collect();

                foreach ($students as $student) {
                    if (in_array($student->id, $existingStudentIds, true)) {
                        continue;
                    }
                    $assignment->submissions()->create([
                        'user_id' => $student->id,
                        'status' => 'assigned',
                    ]);
                }
            });
        }

        return Command::SUCCESS;
    }
}
