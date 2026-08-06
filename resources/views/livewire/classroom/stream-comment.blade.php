<div class="border-t border-gray-100" x-data="{ showDeleteCommentModal: false, deleteCommentId: null }"
    @keydown.escape.window="showDeleteCommentModal = false">
    <button wire:click="toggleComments"
        class="flex items-center gap-2 px-5 py-3 text-sm text-gray-500 hover:text-gray-700 w-full text-left">
        <x-icon name="chat-bubble-left" class="h-4 w-4" />
        {{ $comments->count() }} ความคิดเห็น
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
                                aria-label="ลบความคิดเห็น">
                                <x-icon name="trash" class="h-4 w-4" />
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
                        class="w-full rounded-[10px] border border-gray-300 px-4 py-2 pr-11 text-sm outline-none transition focus:border-[var(--ll-blue)] focus:ring-1 focus:ring-[var(--ll-blue)]"
                        placeholder="เพิ่มความคิดเห็น...">
                    <button wire:click="addComment"
                        class="absolute right-1.5 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-[8px] text-[var(--ll-blue)] transition hover:bg-[rgba(37,99,235,0.08)] hover:text-[var(--ll-blue-dark)]">
                        <x-icon name="paper-airplane" class="h-4 w-4" />
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
                <h4 class="text-lg font-black text-[#101114]">{{ 'ลบความคิดเห็น' }}</h4>
                <p class="mt-2 text-sm text-[#686b82]">{{ 'คุณแน่ใจหรือว่าต้องการลบความคิดเห็นนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้' }}</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="showDeleteCommentModal = false"
                        class="rounded-[10px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:bg-[rgba(37,99,235,0.04)]">
                        {{ 'ยกเลิก' }}
                    </button>
                    <button type="button" @click="$wire.deleteComment(deleteCommentId); showDeleteCommentModal = false"
                        class="rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                        {{ 'ลบ' }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
