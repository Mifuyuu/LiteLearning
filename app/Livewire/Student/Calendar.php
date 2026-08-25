<?php

namespace App\Livewire\Student;

use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Calendar extends Component
{
    public Collection $upcoming;

    public function placeholder()
    {
        return view('livewire.placeholders.calendar');
    }

    public function mount(): void
    {
        $user = auth()->user();
        $upcoming = collect();

        foreach ($user->allClassrooms() as $classroom) {
            $upcoming = $upcoming->merge(
                $classroom->assignments()->published()
                    ->whereNotIn('assignments.type', ['material', 'announcement', 'topic'])
                    ->whereDoesntHave('submissions', function ($query) use ($user): void {
                        $query->where('user_id', $user->id)
                            ->whereIn('status', ['turned_in', 'graded']);
                    })
                    ->with(['classworkItem.classroom.themeCategory', 'submissions' => function ($query) use ($user): void {
                        $query->where('user_id', $user->id);
                    }])
                    ->get()
            );
        }

        $this->upcoming = $upcoming->sort(function ($a, $b) {
            if ($a->due_date === null && $b->due_date === null) {
                return 0;
            }
            if ($a->due_date === null) {
                return 1;
            }
            if ($b->due_date === null) {
                return -1;
            }

            return $a->due_date <=> $b->due_date;
        })->values();
    }

    public function render()
    {
        return view('livewire.student.calendar');
    }
}
