<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\User;
use App\Services\GamificationService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JoinClassroom extends Component
{
    public string $code = '';

    public bool $showModal = false;

    protected $rules = [
        'code' => 'required|string|size:6',
    ];

    protected function messages(): array
    {
        return [
            'code.required' => __('messages.validation.code_classroom'),
        ];
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset('code');
        $this->showModal = true;
    }

    public function join()
    {
        $settings = app(SettingsService::class);

        if (! $settings->bool('classroom_join_enabled', true)) {
            $this->addError('code', __('messages.classroom.join_closed'));

            return;
        }

        // S7: rate limit classroom join attempts (configurable, default 5 per minute)
        $limit = $settings->int('classroom_join_rate_limit', 5);
        $key = 'join-classroom:'.auth()->id();
        if (cache()->has($key) && cache()->get($key) >= $limit) {
            $this->addError('code', __('messages.classroom.join_throttle'));

            return;
        }
        cache()->increment($key);
        cache()->put($key, cache()->get($key), 60);

        $this->validate();

        $classroom = Classroom::where('code', strtoupper($this->code))->first();

        if (! $classroom) {
            $this->addError('code', __('messages.classroom.code_not_found'));

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if ($classroom->isOwnedBy($user)) {
            $this->addError('code', __('messages.classroom.already_owner_join'));

            return;
        }

        if ($classroom->hasMember($user)) {
            $this->addError('code', __('messages.classroom.already_member'));

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
