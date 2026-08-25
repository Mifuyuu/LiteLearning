<?php

namespace App\Livewire\Classroom;

use App\Models\Announcement;
use App\Models\Comment;
use App\Models\Material;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class StreamComment extends Component
{
    #[Locked]
    public int $contentId = 0;

    #[Locked]
    public string $contentType = Announcement::class;

    public string $commentText = '';

    public bool $showComments = false;

    protected $rules = [
        'commentText' => 'required|string|min:1|max:5000',
    ];

    protected function messages(): array
    {
        return [
            'commentText.required' => __('messages.validation.comment'),
        ];
    }

    public function mount(int $announcementId = 0, int $contentId = 0, string $contentType = Announcement::class): void
    {
        $this->contentId = $announcementId !== 0 ? $announcementId : $contentId;
        $this->contentType = $announcementId !== 0 ? Announcement::class : $contentType;

        $this->resolveCommentable();
    }

    public function toggleComments(): void
    {
        $this->showComments = ! $this->showComments;

        if ($this->showComments) {
            $this->dispatch('comment-panel-opened', contentId: $this->contentId, contentType: $this->contentType);
        }
    }

    #[On('comment-panel-opened')]
    public function closeIfDifferent(int $contentId, string $contentType): void
    {
        if ($contentId !== $this->contentId || $contentType !== $this->contentType) {
            $this->showComments = false;
        }
    }

    public function addComment(): void
    {
        $this->validate();

        $commentable = $this->resolveCommentable();

        /** @var User $user */
        $user = Auth::user();

        $comment = Comment::create([
            'commentable_type' => $commentable::class,
            'commentable_id' => $commentable->getKey(),
            'user_id' => $user->id,
            'content' => $this->commentText,
        ]);

        app(GamificationService::class)->awardForCommentCreated($user, $comment->id);

        $this->reset('commentText');
        $this->dispatch('comment-added');
    }

    public function deleteComment(int $commentId): void
    {
        $commentable = $this->resolveCommentable();

        $comment = Comment::where('commentable_type', $commentable::class)
            ->where('commentable_id', $commentable->getKey())
            ->findOrFail($commentId);

        abort_unless(
            $comment->user_id === Auth::id() || $commentable->classroom?->canManageClassroom(Auth::user()),
            403
        );

        $comment->delete();
        $this->dispatch('comment-deleted');
    }

    private function resolveCommentable(): Model
    {
        $allowedTypes = [
            Announcement::class,
            Material::class,
        ];

        abort_unless($this->contentId > 0, 404);
        abort_unless(in_array($this->contentType, $allowedTypes, true), 404);

        $commentable = match ($this->contentType) {
            Announcement::class => Announcement::with('classworkItem.classroom')->findOrFail($this->contentId),
            Material::class => Material::with('classworkItem.classroom')->findOrFail($this->contentId),
        };

        /** @var User $user */
        $user = Auth::user();
        $classroom = $commentable->classroom;

        abort_unless($classroom?->hasAccess($user), 404);

        if ($commentable->classworkItem?->published_at?->isFuture() && ! $classroom->canManageClassroom($user)) {
            abort(404);
        }

        return $commentable;
    }

    public function render()
    {
        $commentable = $this->resolveCommentable();
        $classroom = $commentable->classroom;
        $themeColor = $classroom?->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom?->id ?? 0)['color'];

        $comments = Comment::where('commentable_type', $commentable::class)
            ->where('commentable_id', $commentable->getKey())
            ->with('user')
            ->latest()
            ->get();

        return view('livewire.classroom.stream-comment', [
            'comments' => $comments,
            'canModerate' => $classroom?->canManageClassroom(Auth::user()) ?? false,
            'themeColor' => $themeColor,
        ]);
    }
}
