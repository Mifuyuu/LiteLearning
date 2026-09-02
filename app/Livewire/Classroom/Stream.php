<?php

namespace App\Livewire\Classroom;

use App\Livewire\Concerns\HasFileUpload;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mews\Purifier\Facades\Purifier;

#[Lazy]
#[Layout('layouts.app')]
class Stream extends Component
{
    use HasFileUpload, WithFileUploads;

    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.classroom-stream', $params);
    }
    #[Locked]
    public Classroom $classroom;

    public bool $showDeleteAnnouncementModal = false;

    public ?int $deleteAnnouncementId = null;

    public ?int $editingAnnouncementId = null;

    public string $editContent = '';

    public bool $showDeleteAttachmentModal = false;

    public ?int $deleteAttachmentId = null;

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

        $announcementsQuery = $this->classroom->announcements()->with(['classworkItem.user', 'comments.user', 'attachments']);
        if (! $this->classroom->canManageClassroom($user)) {
            $announcementsQuery->where(function ($query): void {
                $query->whereNull('classwork_items.published_at')
                    ->orWhere('classwork_items.published_at', '<=', now());
            });
        }

        $this->classroom->setRelation('announcements', $announcementsQuery->latest()->get());
    }

    private function authorizeAnnouncementAction(Announcement $announcement): void
    {
        abort_unless($announcement->classroom_id === $this->classroom->id, 404);

        /** @var User $user */
        $user = Auth::user();
        abort_unless($announcement->user_id === $user->id || $this->classroom->canManageClassroom($user), 403);
    }

    protected function allowedMimes(): string
    {
        return Assignment::allowedSubmissionMimes();
    }

    protected function maxFileSizeKb(): int
    {
        return 25600;
    }

    private function persistAttachments(Announcement $announcement): void
    {
        foreach ($this->uploadedFiles as $uploaded) {
            if (! isset($uploaded['file']) || ! $uploaded['file']) {
                continue;
            }

            $path = $uploaded['file']->store('classwork/attachments/'.$this->classroom->id, 's3');
            $announcement->attachments()->create([
                'file_name' => $uploaded['name'],
                'file_path' => $path,
                'file_type' => $uploaded['mime'],
                'file_size' => $uploaded['size'],
                'uploaded_by' => auth()->id(),
            ]);
        }

        $this->uploadedFiles = [];
    }

    public function confirmRemoveAttachment(int $attachmentId): void
    {
        $this->deleteAttachmentId = $attachmentId;
        $this->showDeleteAttachmentModal = true;
    }

    public function removeExistingAttachment(): void
    {
        $announcement = Announcement::findOrFail($this->editingAnnouncementId);
        $this->authorizeAnnouncementAction($announcement);

        $attachment = $announcement->attachments()->findOrFail($this->deleteAttachmentId);
        Storage::disk('s3')->delete($attachment->file_path);
        $attachment->delete();

        $this->showDeleteAttachmentModal = false;
        $this->deleteAttachmentId = null;
        $this->loadClassroomRelations();
    }

    public function confirmDeleteAnnouncement(int $id): void
    {
        $this->deleteAnnouncementId = $id;
        $this->showDeleteAnnouncementModal = true;
    }

    public function startEditAnnouncement(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $this->authorizeAnnouncementAction($announcement);

        $this->editingAnnouncementId = $id;
        $this->editContent = $announcement->content ?? '';
        $this->uploadedFiles = [];
    }

    public function cancelEditAnnouncement(): void
    {
        $this->editingAnnouncementId = null;
        $this->editContent = '';
        $this->uploadedFiles = [];
        $this->showDeleteAttachmentModal = false;
        $this->deleteAttachmentId = null;
    }

    public function updateAnnouncement(): void
    {
        $announcement = Announcement::findOrFail($this->editingAnnouncementId);
        $this->authorizeAnnouncementAction($announcement);

        $this->validate(['editContent' => 'required|string']);

        $announcement->update(['content' => Purifier::clean($this->editContent)]);
        $this->persistAttachments($announcement);

        $this->editingAnnouncementId = null;
        $this->editContent = '';
        $this->loadClassroomRelations();
    }

    public function deleteAnnouncement(?int $id = null): void
    {
        $announcementId = $id ?? $this->deleteAnnouncementId;

        if (! $announcementId) {
            return;
        }

        $announcement = Announcement::with('classworkItem')->findOrFail($announcementId);
        $this->authorizeAnnouncementAction($announcement);

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
            ->title($this->classroom->name.' - '.'กระดานสนทนา');
    }
}
