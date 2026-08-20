<?php

namespace App\Livewire;

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
                    ->whereNotNull('due_date')
                    ->whereDoesntHave('submissions', function ($query) use ($user): void {
                        $query->where('user_id', $user->id)
                            ->whereIn('status', ['turned_in', 'graded', 'returned']);
                    })
                    ->with('classworkItem.classroom.themeCategory')
                    ->orderBy('due_date')
                    ->get()
            );
        }

        $this->upcoming = $upcoming->sortBy('due_date')->take(20)->values();
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
