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
        $key = 'join-classroom:'.auth()->id();
        if (cache()->has($key) && cache()->get($key) >= 5) {
            $this->addError('code', 'พยายามมากเกินไป กรุณารอสักครู่');

            return;
        }
        cache()->increment($key);
        cache()->put($key, cache()->get($key), 60);

        $this->validate();

        $classroom = Classroom::where('code', strtoupper($this->code))->first();

        if (! $classroom) {
            $this->addError('code', 'ไม่พบห้องเรียนด้วยรหัสนี้');

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if ($classroom->isOwnedBy($user)) {
            $this->addError('code', 'คุณเป็นครูเจ้าของห้องเรียนนี้');

            return;
        }

        if ($classroom->hasMember($user)) {
            $this->addError('code', 'คุณเป็นสมาชิกของห้องเรียนนี้อยู่แล้ว');

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
