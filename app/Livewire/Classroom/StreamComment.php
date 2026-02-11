<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StreamComment extends Component
{
    public int $announcementId;
    public string $commentText = '';
    public bool $showComments = false;

    protected $rules = [
        'commentText' => 'required|string|min:1|max:5000',
    ];

    public function toggleComments()
    {
        $this->showComments = !$this->showComments;
    }

    public function addComment()
    {
        $this->validate();

        // Verify user has access to the announcement's classroom
        $announcement = \App\Models\Announcement::findOrFail($this->announcementId);
        $classroom = $announcement->classroom;

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$classroom->hasAccess($user)) {
            abort(403);
        }

        Comment::create([
            'commentable_type' => \App\Models\Announcement::class,
            'commentable_id' => $this->announcementId,
            'user_id' => Auth::id(),
            'content' => $this->commentText,
        ]);

        $this->reset('commentText');
        $this->dispatch('comment-added');
    }

    public function render()
    {
        $comments = Comment::where('commentable_type', \App\Models\Announcement::class)
            ->where('commentable_id', $this->announcementId)
            ->with('user')
            ->latest()
            ->get();

        return view('livewire.classroom.stream-comment', [
            'comments' => $comments,
        ]);
    }
}
