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
        // S7: rate limit classroom join attempts
        $key = 'join-classroom:' . auth()->id();
        if (cache()->has($key) && cache()->get($key) >= 5) {
            $this->addError('code', __('Too many attempts. Please wait a minute.'));
            return;
        }
        cache()->increment($key);
        cache()->put($key, cache()->get($key), 60);

        $this->validate();

        $classroom = Classroom::where('code', strtoupper($this->code))->first();

        if (!$classroom) {
            $this->addError('code', __('No classroom found with this code.'));
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if ($classroom->isOwnedBy($user)) {
            $this->addError('code', __('You are the teacher of this classroom.'));
            return;
        }

        if ($classroom->hasMember($user)) {
            $this->addError('code', __('You are already a member of this classroom.'));
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
