@section('page-title', 'กระดานสนทนา' . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ $classroom->name }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'กระดานสนทนา' }}</span>
    </nav>
@endsection

@php
    $manager = $classroom->canManageClassroom(auth()->user());
@endphp

<div class="space-y-5 ">
    <section class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ 'กระดานสนทนา' }}</p>
                <h1 class="mt-1 text-2xl font-black text-[#101114]">{{ 'อัปเดตล่าสุด' }}</h1>
            </div>
            @if($manager)
                <div class="dropdown dropdown-end">
                    <button tabindex="0" class="inline-flex items-center gap-2 rounded-[10px] bg-[var(--ll-blue)] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[var(--ll-blue-dark)]">
                        <x-icon name="plus" class="h-4 w-4" />
                        {{ 'สร้าง' }}
                        <x-icon name="chevron-down" class="h-3.5 w-3.5" />
                    </button>
                    <ul tabindex="0" class="dropdown-content menu z-50 mt-2 w-44 rounded-[12px] border border-[#dedee5] bg-white p-1.5 shadow-lg">
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=question" wire:navigate class="rounded-[8px] text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="pencil" class="h-4 w-4" />
                                {{ 'งาน' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=announcement" wire:navigate class="rounded-[8px] text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="chat-bubble-left-ellipsis" class="h-4 w-4" />
                                {{ 'ประกาศ' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=attendance" wire:navigate class="rounded-[8px] text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="check-circle" class="h-4 w-4" />
                                {{ 'เช็คชื่อ' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=file" wire:navigate class="rounded-[8px] text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="document" class="h-4 w-4" />
                                {{ 'ไฟล์' }}
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="mt-5 space-y-3">
            @forelse($classroom->announcements as $announcement)
                <article class="rounded-[12px] border border-[#dedee5] bg-[rgba(37,99,235,0.02)] p-4">
                    <div class="flex items-start gap-3">
                        <img src="{{ $announcement->user->avatar_url }}" alt="{{ $announcement->user->name }}"
                            class="h-10 w-10 shrink-0 rounded-[10px] object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-extrabold text-[#101114]">{{ $announcement->user->name }}</p>
                                    <p class="text-xs font-semibold text-[#9497a9]">{{ $announcement->created_at->diffForHumans() }}</p>
                                </div>
                                @if($announcement->user_id === auth()->id() || $manager)
                                    <button type="button" wire:click="confirmDeleteAnnouncement({{ $announcement->id }})"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-[8px] text-rose-400 transition hover:bg-rose-50 hover:text-rose-600"
                                        title="{{ 'ลบ' }}">
                                        <x-icon name="trash" class="h-3.5 w-3.5" />
                                    </button>
                                @endif
                            </div>
                            <div class="prose prose-sm mt-3 max-w-none text-[#686b82] [&_p:first-child]:mt-0 [&_p:last-child]:mb-0">
                                {!! $announcement->content !!}
                            </div>
                        </div>
                    </div>
                    @livewire('classroom.stream-comment', ['announcementId' => $announcement->id], "stream-comment-{$announcement->id}")
                </article>
            @empty
                <x-empty-state-inline title="กระดานสนทนา" body="ยังไม่มีประกาศ" />
            @endforelse
        </div>
    </section>

    <div x-data x-show="$wire.showDeleteAnnouncementModal" x-cloak
        class="fixed inset-0 z-70 flex items-center justify-center bg-black/50 p-4"
        @click.self="$wire.set('showDeleteAnnouncementModal', false)">
        <div class="w-full max-w-md rounded-[12px] border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
            <h4 class="text-lg font-black text-[#101114]">{{ 'ลบประกาศ' }}</h4>
            <p class="mt-2 text-sm text-[#686b82]">{{ 'คุณแน่ใจหรือว่าต้องการลบประกาศนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้' }}</p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$wire.set('showDeleteAnnouncementModal', false)"
                    class="rounded-[10px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:bg-[var(--ll-blue-faint)]">
                    {{ 'ยกเลิก' }}
                </button>
                <button type="button" wire:click="deleteAnnouncement"
                    class="rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                    {{ 'ลบ' }}
                </button>
            </div>
        </div>
    </div>
</div>
