<?php

namespace App\Livewire\Assignment;

use App\Livewire\Concerns\VerifiesContentAccess;
use App\Models\Assignment;
use App\Models\AttendanceSession as AttendanceSessionModel;
use App\Models\Classroom;
use App\Models\Submission;
use App\Services\GamificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class Attendance extends Component
{
    use VerifiesContentAccess;

    #[Locked]
    public Classroom $classroom;

    #[Locked]
    public Assignment $assignment;

    public ?AttendanceSessionModel $session = null;

    public bool $sessionIsActive = false;

    // Student
    public string $enteredCode = '';

    public bool $alreadyCheckedIn = false;

    public function mount(Classroom $classroom, Assignment $assignment): void
    {
        $this->classroom = $classroom;
        $this->assignment = $assignment;

        // Verify assignment belongs to this classroom + user has access
        $this->verifyContentAccess($classroom, $assignment);

        $this->session = $assignment->attendanceSession;

        // Check if student already checked in
        if (auth()->user()->isStudent()) {
            $submission = $assignment->submissionFor(auth()->user());
            $this->alreadyCheckedIn = $submission?->isTurnedIn() ?? false;
        }
    }

    // ──────────────────────────────────────────────
    // Teacher actions
    // ──────────────────────────────────────────────

    public function startSession(): void
    {
        abort_unless(
            $this->classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin(),
            403
        );

        if (! $this->session) {
            $this->session = AttendanceSessionModel::create([
                'assignment_id' => $this->assignment->id,
                'is_active' => false,
            ]);
        }

        $this->session->start();
    }

    public function stopSession(): void
    {
        abort_unless(
            $this->classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin(),
            403
        );

        $this->session?->stop();
        $this->session?->refresh();
    }

    /**
     * Called by wire:poll every 10 seconds to rotate the code.
     */
    public function rotateCode(): void
    {
        if (! $this->session?->is_active) {
            return;
        }

        // Only teacher can trigger rotation
        if ($this->classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin()) {
            if ($this->session->isCodeExpired()) {
                $this->session->generateNewCode();
            }
        }

        $this->session->refresh();
    }

    // ──────────────────────────────────────────────
    // Student actions
    // ──────────────────────────────────────────────

    public function checkin(): void
    {
        $user = auth()->user();

        // Must be a student
        abort_unless($user->isStudent(), 403);

        // Session must be active
        if (! $this->session?->is_active) {
            session()->flash('attendance_error', __('Attendance session is not active'));

            return;
        }

        // Already checked in
        if ($this->alreadyCheckedIn) {
            session()->flash('attendance_error', __('Already checked in'));

            return;
        }

        // Validate code
        $this->session->refresh();
        if ($this->enteredCode !== $this->session->current_code) {
            session()->flash('attendance_error', __('Invalid code'));
            $this->enteredCode = '';

            return;
        }

        // Create or update submission
        $submission = $this->assignment->submissionFor($user);
        if (! $submission) {
            $submission = $this->assignment->submissions()->create([
                'user_id' => $user->id,
                'status' => 'assigned',
            ]);
        }

        $submission->update([
            'content' => __('Checked in at :time', ['time' => now()->format('H:i:s')]),
        ]);
        $submission->turnIn();

        $this->alreadyCheckedIn = true;
        $this->enteredCode = '';

        app(GamificationService::class)->awardForAssignmentTurnedIn($user, $this->assignment->id);

        session()->flash('attendance_success', __('Checked in successfully'));
    }

    public function getCheckedInStudentsProperty()
    {
        return $this->assignment->submissions()
            ->with('user')
            ->whereIn('status', ['turned_in', 'graded', 'returned'])
            ->get();
    }

    public function render()
    {
        return view('livewire.assignment.attendance');
    }
}
