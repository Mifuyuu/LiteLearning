<?php

namespace App\Livewire\Assignment;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public Classroom $classroom;
    public string $title = '';
    public string $description = '';
    public string $instructions = '';
    public int $max_score = 100;
    public ?string $due_date = null;
    public string $type = 'assignment';
    public string $topic = '';
    public string $status = 'published';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'instructions' => 'nullable|string',
        'max_score' => 'required|integer|min:0|max:1000',
        'due_date' => 'nullable|date',
        'type' => 'required|in:assignment,quiz,material',
        'topic' => 'nullable|string|max:255',
        'status' => 'required|in:draft,published',
    ];

    public function mount(Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }
        $this->classroom = $classroom;
    }

    public function save()
    {
        $this->validate();

        $assignment = Assignment::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'instructions' => $this->instructions,
            'max_score' => $this->type === 'material' ? 0 : $this->max_score,
            'due_date' => $this->type === 'material' ? null : $this->due_date,
            'type' => $this->type,
            'topic' => $this->topic,
            'status' => $this->status,
        ]);

        // Create submissions for all enrolled students (for assignments and quizzes)
        if ($this->type !== 'material' && $this->status === 'published') {
            foreach ($this->classroom->students as $student) {
                $assignment->submissions()->create([
                    'user_id' => $student->id,
                    'status' => 'assigned',
                ]);
            }
        }

        session()->flash('message', 'Assignment created successfully!');

        return redirect()->route('assignment.show', [
            'classroom' => $this->classroom,
            'assignment' => $assignment,
        ]);
    }

    public function render()
    {
        return view('livewire.assignment.create', [
            'topics' => $this->classroom->topics,
        ]);
    }
}
