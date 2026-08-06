<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Submission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.app')]
class ToReview extends Component
{
    use WithPagination;

    public ?string $classroomId = null;

    public string $search = '';

    public string $sortField = 'turned_in_at';

    public string $sortDir = 'desc';

    public Collection $classrooms;

    protected $queryString = [
        'classroomId' => ['except' => ''],
        'search' => ['except' => ''],
        'sortField' => ['except' => 'turned_in_at'],
        'sortDir' => ['except' => 'desc'],
    ];

    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.generic', ['pageTitle' => 'รอตรวจ']);
    }

    public function mount(): void
    {
        $user = auth()->user();
        $this->classrooms = $user->allClassrooms()->load('themeCategory');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedClassroomId(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
    }

    public function getPendingSubmissionsProperty(): LengthAwarePaginator
    {
        $user = auth()->user();
        $classroomIds = $this->classrooms->pluck('id');

        $query = Submission::query()
            ->where('status', 'turned_in')
            ->whereHas('assignment.classworkItem', function ($q) use ($classroomIds) {
                $q->whereIn('classroom_id', $classroomIds);

                if ($this->classroomId) {
                    $q->where('classroom_id', $this->classroomId);
                }
            })
            ->with([
                'user',
                'assignment' => fn ($q) => $q->with('classworkItem.classroom.themeCategory'),
            ])
            ->orderBy($this->sortField, $this->sortDir);

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.to-review', [
            'pendingSubmissions' => $this->pendingSubmissions,
        ]);
    }
}
