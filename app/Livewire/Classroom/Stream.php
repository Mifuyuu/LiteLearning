<?php

namespace App\Livewire\Classroom;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Stream extends Component
{
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.classroom-stream', $params);
    }
    #[Locked]
    public Classroom $classroom;

    public bool $showDeleteAnnouncementModal = false;

    public ?int $deleteAnnouncementId = null;

    public function mount(Classroom $classroom): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($classroom->hasAccess($user), 404);

        $this->classroom = $classroom;
        $this->loadClassroomRelations();
    }

    private function loadClassroomRelations(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->classroom->load(['teacher', 'students', 'coTeachers']);

        $announcementsQuery = $this->classroom->announcements()->with(['classworkItem.user', 'comments.user']);
        if (! $this->classroom->canManageClassroom($user)) {
            $announcementsQuery->where(function ($query): void {
                $query->whereNull('classwork_items.published_at')
                    ->orWhere('classwork_items.published_at', '<=', now());
            });
        }

        $this->classroom->setRelation('announcements', $announcementsQuery->latest()->get());
    }

    public function confirmDeleteAnnouncement(int $id): void
    {
        $this->deleteAnnouncementId = $id;
        $this->showDeleteAnnouncementModal = true;
    }

    public function deleteAnnouncement(?int $id = null): void
    {
        $announcementId = $id ?? $this->deleteAnnouncementId;

        if (! $announcementId) {
            return;
        }

        $announcement = Announcement::findOrFail($announcementId);
        abort_unless($announcement->classroom_id === $this->classroom->id, 404);

        /** @var User $user */
        $user = Auth::user();
        if ($announcement->user_id !== $user->id && ! $this->classroom->canManageClassroom($user)) {
            abort(403);
        }

        DB::transaction(function () use ($announcement): void {
            foreach ($announcement->attachments as $attachment) {
                Storage::disk('s3')->delete($attachment->file_path);
                $attachment->delete();
            }

            $announcement->comments()->delete();
            $announcement->classworkItem?->delete();
        });

        $this->showDeleteAnnouncementModal = false;
        $this->deleteAnnouncementId = null;
        $this->loadClassroomRelations();
    }

    public function render()
    {
        return view('livewire.classroom.stream')
            ->title($this->classroom->name.' - '.__('กระดานสนทนา'));
    }
}
