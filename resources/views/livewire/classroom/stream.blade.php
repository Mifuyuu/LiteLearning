@section('page-title', 'กระดานสนทนา' . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">
            {{ \Illuminate\Support\Str::limit($classroom->name, 15, '..') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'กระดานสนทนา' }}</span>
    </nav>
@endsection

@php
    $manager = $classroom->canManageClassroom(auth()->user());
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;">
    <section class="rounded-2xl border-3 border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)]">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                {{-- <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ 'กระดานสนทนา' }}</p> --}}
                <h1 class="mt-1 text-2xl font-black text-[#101114]">{{ 'กระดานสนทนา' }}</h1>
            </div>
            @if($manager)
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="inline-flex cursor-pointer items-center gap-1 sm:gap-2 rounded-[10px] bg-(--cw-color) px-3 sm:px-4 py-2.5 text-sm font-extrabold text-white transition hover:opacity-90 shadow-sm">
                        <x-icon name="plus" class="h-4 w-4" />
                        <span class="hidden sm:inline">{{ 'สร้าง' }}</span>
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 transition-transform" ::class="open ? 'rotate-180' : ''" />
                    </button>
                    <ul x-show="open" x-cloak
                        class="absolute menu right-0 top-full z-50 mt-2 w-44 rounded-xl border border-[#dedee5] bg-white p-1.5 shadow-lg">
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=file" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-[#101114] hover:bg-(--cw-faint) hover:text-(--cw-color)">
                                <x-icon name="document-text" class="h-4 w-4 shrink-0" />
                                {{ 'งานส่งไฟล์' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=announcement" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-[#101114] hover:bg-(--cw-faint) hover:text-(--cw-color)">
                                <x-icon name="megaphone" class="h-4 w-4 shrink-0" />
                                {{ 'ประกาศ' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('material.create', $classroom) }}" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-[#101114] hover:bg-(--cw-faint) hover:text-(--cw-color)">
                                <x-icon name="book-open" class="h-4 w-4 shrink-0" />
                                {{ 'สื่อการสอน' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=attendance" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-[#101114] hover:bg-(--cw-faint) hover:text-(--cw-color)">
                                <x-icon name="pencil-square" class="h-4 w-4 shrink-0" />
                                {{ 'งานเช็คชื่อ' }}
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="mt-5 space-y-3">
            @forelse($classroom->announcements as $announcement)
                <article class="rounded-xl border border-[#dedee5] bg-[rgba(37,99,235,0.02)] p-4">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <img src="{{ $announcement->user->avatar_url }}" alt="{{ $announcement->user->name }}"
                                        class="h-8 w-8 shrink-0 rounded-lg object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-extrabold text-[#101114]">{{ $announcement->user->name }}</p>
                                        <p class="text-xs font-semibold text-[#9497a9]">{{ $announcement->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @if($announcement->user_id === auth()->id() || $manager)
                                    <div class="relative shrink-0" x-data="{ open: false }">
                                        <button type="button" @click.stop="open = !open"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[#686b82] transition hover:bg-gray-100 hover:text-[#101114] cursor-pointer"
                                            title="{{ 'ตัวเลือก' }}">
                                            <x-icon name="ellipsis-vertical" class="h-4 w-4" />
                                        </button>
                                        <div x-show="open" x-cloak @click.outside="open = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute right-0 top-full z-20 mt-1 w-36 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                                            <button type="button" wire:click="startEditAnnouncement({{ $announcement->id }})" @click="open = false"
                                                class="flex w-full items-center px-3.5 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50">
                                                <x-icon name="pencil" class="h-4 w-4 text-gray-400" />
                                                <span class="ml-2">{{ 'แก้ไข' }}</span>
                                            </button>
                                            <button type="button" wire:click="confirmDeleteAnnouncement({{ $announcement->id }})" @click="open = false"
                                                class="flex w-full items-center px-3.5 py-2 text-sm text-rose-600 transition-colors hover:bg-rose-50">
                                                <x-icon name="trash" class="h-4 w-4" />
                                                <span class="ml-2">{{ 'ลบ' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($editingAnnouncementId === $announcement->id)
                                <div class="mt-2" wire:key="edit-announcement-{{ $announcement->id }}">
                                    <div wire:ignore x-data="tiptapEditor({ wireModel: 'editContent', placeholder: 'เพิ่มเนื้อหา...' })">
                                        <x-tiptap-toolbar />
                                        <div x-ref="editorEl"
                                            class="min-h-24 border border-gray-200 rounded-b-lg p-3 bg-white focus:outline-none prose prose-sm max-w-none">
                                        </div>
                                    </div>
                                    @error('editContent')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror

                                    {{-- Existing attachments --}}
                                    @if($announcement->attachments->isNotEmpty())
                                        <div class="mt-3 space-y-2">
                                            @foreach($announcement->attachments as $attachment)
                                                <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl border border-[#dedee5] bg-white"
                                                    wire:key="edit-attach-{{ $attachment->id }}">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <div class="w-9 h-9 rounded-lg bg-[#f9f9fb] border border-[#dedee5] flex items-center justify-center shrink-0 text-[#686b82]">
                                                            <x-icon :name="$attachment->icon" class="h-5 w-5" />
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-xs font-semibold text-[#101114] truncate">{{ $attachment->file_name }}</p>
                                                            <p class="text-[11px] text-[#9497a9]">{{ $attachment->formatted_size }}</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" wire:click="confirmRemoveAttachment({{ $attachment->id }})"
                                                        class="text-[#9497a9] hover:text-red-500 shrink-0 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 transition-colors cursor-pointer"
                                                        title="{{ 'ลบไฟล์แนบ' }}">
                                                        <x-icon name="trash" class="h-4 w-4" />
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Add file --}}
                                    <div class="mt-3">
                                        <label for="edit-announcement-file-{{ $announcement->id }}"
                                            class="relative flex items-center justify-center gap-2 w-full h-14 border-2 border-[#dedee5] border-dashed rounded-xl cursor-pointer bg-[#f9f9fb] hover:bg-(--cw-faint) hover:border-(--cw-color) transition-colors">
                                            <x-icon name="arrow-up-tray" class="h-4 w-4 text-[#9497a9]" />
                                            <span class="text-sm text-[#686b82]"><span class="font-bold">{{ 'คลิกเพื่ออัปโหลด' }}</span> {{ 'หรือลากและวางไฟล์' }}</span>
                                            <input id="edit-announcement-file-{{ $announcement->id }}" type="file" wire:model.live="file" multiple class="hidden" />
                                        </label>
                                        @error('file.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>

                                    @if(count($uploadedFiles) > 0)
                                        <div class="mt-2 space-y-2">
                                            @foreach($uploadedFiles as $index => $uploadedFile)
                                                <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl border border-[#dedee5] bg-white"
                                                    wire:key="staged-attach-{{ $uploadedFile['id'] }}">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <div class="w-9 h-9 rounded-lg bg-(--cw-faint) text-(--cw-color) flex items-center justify-center shrink-0">
                                                            <x-icon name="document-text" class="h-5 w-5" />
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-xs font-semibold text-[#101114] truncate">{{ $uploadedFile['name'] }}</p>
                                                            <p class="text-[11px] text-[#9497a9]">{{ number_format($uploadedFile['size'] / 1024 / 1024, 2) }} MB</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" wire:click="removeFile({{ $index }})"
                                                        class="text-[#9497a9] hover:text-red-500 shrink-0 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 transition-colors cursor-pointer"
                                                        title="{{ 'ลบ' }}">
                                                        <x-icon name="trash" class="h-4 w-4" />
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="mt-3 flex items-center gap-2">
                                        <button type="button" wire:click="updateAnnouncement"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg text-white bg-(--cw-color) hover:opacity-90 transition-colors cursor-pointer">
                                            {{ 'บันทึก' }}
                                        </button>
                                        <button type="button" wire:click="cancelEditAnnouncement"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg border border-[#dedee5] bg-white text-[#686b82] hover:bg-gray-100 transition-colors cursor-pointer">
                                            {{ 'ยกเลิก' }}
                                        </button>
                                    </div>
                                </div>
                            @else
                                @if($announcement->content)
                                    <div class="prose prose-sm mt-2 max-w-none text-[#686b82] [&_p:first-child]:mt-0 [&_p:last-child]:mb-0">
                                        {!! $announcement->content !!}
                                    </div>
                                @endif

                                {{-- Attachments --}}
                                @if($announcement->attachments->isNotEmpty())
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        @foreach($announcement->attachments as $attachment)
                                            @php
                                                $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $url = \Illuminate\Support\Facades\Storage::disk('s3')->url($attachment->file_path);
                                            @endphp
                                            <a href="{{ $url }}" target="_blank"
                                                class="flex items-center gap-3 p-2.5 rounded-xl border border-[#dedee5] bg-white hover:border-(--cw-color)/40 hover:bg-(--cw-faint) transition-colors group">
                                                <div class="w-9 h-9 rounded-lg bg-[#f9f9fb] border border-[#dedee5] flex items-center justify-center shrink-0 text-[#686b82] group-hover:text-(--cw-color)">
                                                    @if($isImage)
                                                        <x-icon name="photo" class="h-5 w-5" />
                                                    @else
                                                        <x-icon name="document-text" class="h-5 w-5" />
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-semibold text-[#101114] truncate group-hover:text-(--cw-color)">{{ $attachment->file_name }}</p>
                                                    <p class="text-[11px] text-[#9497a9]">{{ number_format($attachment->file_size / 1024 / 1024, 2) }} MB</p>
                                                </div>
                                                <x-icon name="arrow-top-right-on-square" class="h-4 w-4 text-[#9497a9] shrink-0 group-hover:text-(--cw-color)" />
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                    @livewire('classroom.stream-comment', ['announcementId' => $announcement->id], "stream-comment-{$announcement->id}")
                </article>
            @empty
                <x-empty-state-inline title="กระดานสนทนา" body="ยังไม่มีประกาศ" />
            @endforelse
        </div>
    </section>

    <template x-teleport="body">
        <x-confirm-modal show="$wire.showDeleteAnnouncementModal" cancel="$wire.set('showDeleteAnnouncementModal', false)"
            heading="ลบประกาศ" message="คุณแน่ใจหรือว่าต้องการลบประกาศนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้">
            <button type="button" wire:click="deleteAnnouncement"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                {{ 'ลบ' }}
            </button>
        </x-confirm-modal>
    </template>

    <template x-teleport="body">
        <x-confirm-modal show="$wire.showDeleteAttachmentModal" cancel="$wire.set('showDeleteAttachmentModal', false)"
            heading="ลบไฟล์แนบ" message="คุณแน่ใจหรือว่าต้องการลบไฟล์แนบนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้">
            <button type="button" wire:click="removeExistingAttachment"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                {{ 'ลบ' }}
            </button>
        </x-confirm-modal>
    </template>
</div>
