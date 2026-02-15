<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JoinClassroom extends Component
{
    public string $code = '';
    public bool $showModal = false;

    protected $rules = [
        'code' => 'required|string|size:6',
    ];

    public function openModal()
    {
        $this->resetValidation();
        $this->reset('code');
        $this->showModal = true;
    }

    public function join()
    {
        $this->validate();

        $classroom = Classroom::where('code', strtoupper($this->code))->first();

        if (!$classroom) {
            $this->addError('code', 'No classroom found with this code.');
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if ($classroom->isOwnedBy($user)) {
            $this->addError('code', 'You are the teacher of this classroom.');
            return;
        }

        if ($classroom->hasMember($user)) {
            $this->addError('code', 'You are already a member of this classroom.');
            return;
        }

        $classroom->members()->attach($user->id, [
            'role' => 'student',
            'joined_at' => now(),
        ]);

        app(GamificationService::class)->awardForClassroomJoined($user, $classroom->id);

        $this->showModal = false;

        return redirect()->route('classroom.show', $classroom);
    }

    public function render()
    {
        return view('livewire.classroom.join-classroom');
    }
}
