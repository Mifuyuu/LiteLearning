<div class="border-t border-gray-100" x-data="{ showDeleteCommentModal: false, deleteCommentId: null }"
    @keydown.escape.window="showDeleteCommentModal = false">
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
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span
                                    class="max-w-[100px] truncate text-xs font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                <span class="shrink-0 text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                        </div>
                        @if($comment->user_id === auth()->id())
                            <button type="button"
                                @click="deleteCommentId = {{ $comment->id }}; showDeleteCommentModal = true"
                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-[8px] text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                                aria-label="{{ __('Delete comment') }}">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Add comment -->
            <div class="flex items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-full">
                <div class="flex-1 relative">
                    <input wire:model="commentText" wire:keydown.enter="addComment" type="text"
                        class="w-full rounded-[10px] border border-gray-300 px-4 py-2 pr-11 text-sm outline-none transition focus:border-[#7132f5] focus:ring-1 focus:ring-[#7132f5]"
                        placeholder="{{ __('Add class comment...') }}">
                    <button wire:click="addComment"
                        class="absolute right-1.5 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-[8px] text-[#7132f5] transition hover:bg-[rgba(113,50,245,0.08)] hover:text-[#5741d8]">
                        <i class="fas fa-paper-plane text-base"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <template x-teleport="body">
        <div x-show="showDeleteCommentModal" x-cloak
            class="fixed inset-0 z-70 flex items-center justify-center bg-black/50 p-4"
            @click.self="showDeleteCommentModal = false">
            <div x-show="showDeleteCommentModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md rounded-[12px] border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
                <h4 class="text-lg font-black text-[#101114]">{{ __('Delete comment') }}</h4>
                <p class="mt-2 text-sm text-[#686b82]">{{ __('Are you sure you want to delete this comment? This action cannot be undone.') }}</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="showDeleteCommentModal = false"
                        class="rounded-[10px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:bg-[rgba(133,91,251,0.04)]">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" @click="$wire.deleteComment(deleteCommentId); showDeleteCommentModal = false"
                        class="rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
