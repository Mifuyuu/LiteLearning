<div class="border-t border-gray-100">
    <button wire:click="toggleComments"
        class="flex items-center gap-2 px-5 py-3 text-sm text-gray-500 hover:text-gray-700 w-full text-left">
        <i class="fas fa-comment text-xs"></i>
        {{ $comments->count() }} {{ $comments->count() !== 1 ? __('class comments') : __('class comment') }}
    </button>

    @if($showComments)
        <div class="px-5 pb-4">
            <!-- Comments list -->
            <div class="space-y-3 mb-3">
                @foreach($comments as $comment)
                    <div class="flex items-start gap-2">
                        <img src="{{ $comment->user->avatar_url }}" class="w-7 h-7 rounded-full mt-0.5">
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-xs font-semibold text-gray-900 truncate max-w-[100px]">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Add comment -->
            <div class="flex items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-full">
                <div class="flex-1 relative">
                    <input wire:model="commentText" wire:keydown.enter="addComment" type="text"
                        class="w-full border border-gray-300 rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                        placeholder="{{ __('Add class comment...') }}">
                    <button wire:click="addComment"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-indigo-600 hover:text-indigo-700 p-1">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>