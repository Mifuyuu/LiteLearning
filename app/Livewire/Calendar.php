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
        $upcoming = collect();

        foreach (auth()->user()->allClassrooms() as $classroom) {
            $upcoming = $upcoming->merge(
                $classroom->assignments()->published()
                    ->with('classworkItem.classroom.themeCategory')
                    ->where('due_date', '>=', now())
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
