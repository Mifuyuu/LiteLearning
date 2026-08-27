@section('page-title', $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]" title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 15, '..') }}</span>
    </nav>
@endsection

@php
    $manager = $classroom->canManageClassroom(auth()->user());
    $fallback = \App\Models\ThemeCategory::fallbackFor($classroom->id);
    $themeColor = $classroom->themeCategory?->color ?? $fallback['color'];
    $planetKey = $classroom->themeCategory?->planet_key ?? $fallback['planet_key'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;">
    <section class="overflow-hidden rounded-xl border-3 border-[#dedee5] bg-white min-h-[calc(100vh-3rem)]">

        {{-- Theme banner --}}
        <div class="relative h-32 w-full overflow-hidden sm:h-40" style="background-color: {{ $themeColor }};">
            <img src="/images/planets/planet_{{ $planetKey }}.svg" alt="{{ $classroom->themeCategory?->name ?? $classroom->name }}"
                class="absolute inset-0 h-full w-full scale-[1.75] select-none object-cover" />
        </div>

        {{-- Header --}}
        <div class="p-5 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-extrabold uppercase text-[#9497a9]">{{ 'หน้าหลัก' }}</p>
                    <h1 class="mt-2 text-2xl font-black leading-tight text-[#101114] sm:text-3xl">{{ $classroom->name }}</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-[#686b82]">
                        {{ $classroom->description ?: 'ภาพรวมอย่างรวดเร็วของห้องเรียนนี้' }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-(--cw-subtle) px-3 py-1.5 text-(--cw-color)">
                            <x-icon name="user" class="h-3.5 w-3.5" />
                            {{ $classroom->teacher->name }}
                        </span>
                        @if($classroom->section)
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-[rgba(104,107,130,0.12)] px-3 py-1.5 text-[#484b5e]">
                                <x-icon name="bookmark" class="h-3.5 w-3.5" />
                                {{ $classroom->section }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex shrink-0 flex-wrap gap-2">
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
            </div>
        </div>

        {{-- Quick links: 3 sections divided inside the box --}}
        <div class="grid divide-y divide-[#f2eff5] border-t border-[#f2eff5] lg:grid-cols-3 lg:divide-x lg:divide-y-0">
            <a href="{{ route('classroom.stream', $classroom) }}" wire:navigate
                class="block p-6 transition hover:bg-(--cw-faint)">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg"
                        style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                        <x-icon name="chat-bubble-left-ellipsis" class="h-5 w-5" />
                    </span>
                    <span class="text-2xl font-black text-[#101114]">{{ $classroom->announcements->count() }}</span>
                </div>
                <h2 class="mt-4 text-lg font-black text-[#101114]">{{ 'กระดานสนทนา' }}</h2>
                <p class="mt-1 text-sm leading-6 text-[#686b82]">{{ 'ประกาศและความคิดเห็นในชั้นเรียน' }}</p>
            </a>

            <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
                class="block p-6 transition hover:bg-(--cw-faint)">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-[10px]"
                        style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                        <x-icon name="clipboard-document-list" class="h-5 w-5" />
                    </span>
                    <span class="text-2xl font-black text-[#101114]">{{ $assignmentCount }}</span>
                </div>
                <h2 class="mt-4 text-lg font-black text-[#101114]">{{ 'งานในชั้นเรียน' }}</h2>
                <p class="mt-1 text-sm leading-6 text-[#686b82]">{{ 'งานที่มอบหมาย, งานที่รอทำ, และงานที่ทำเสร็จแล้ว' }}</p>
            </a>

            <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-first-name']) }}" wire:navigate
                class="block p-6 transition hover:bg-(--cw-faint)">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-[10px]"
                        style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
                    <span class="text-2xl font-black text-[#101114]">{{ $students->count() }}</span>
                </div>
                <h2 class="mt-4 text-lg font-black text-[#101114]">{{ 'สมาชิก' }}</h2>
                <p class="mt-1 text-sm leading-6 text-[#686b82]">{{ 'ครู, ผู้สอนร่วม และนักเรียน' }}</p>
            </a>
        </div>

        {{-- กระดานสนทนา --}}
        <div class="border-t border-[#f2eff5] p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="mt-1 text-xl font-black text-[#101114]">{{ 'กระดานสนทนาล่าสุด' }}</h2>
                </div>
                <a href="{{ route('classroom.stream', $classroom) }}" wire:navigate
                    class="text-sm font-bold text-(--cw-color) hover:opacity-80 hover:underline">{{ 'ดูทั้งหมด' }}</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($classroom->announcements->take(3) as $announcement)
                    <a href="{{ route('classroom.stream', $classroom) }}" wire:navigate
                        class="block rounded-[10px] border border-[#dedee5] px-3 py-3 transition hover:border-(--cw-color)/30 hover:bg-(--cw-faint)">
                        <div class="flex items-start gap-3">
                            <img src="{{ $announcement->user->avatar_url }}" alt="{{ $announcement->user->name }}"
                                class="h-8 w-8 shrink-0 rounded-lg object-cover">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-extrabold text-[#101114]">{{ $announcement->user->name }}</p>
                                    <p class="shrink-0 text-xs font-semibold text-[#9497a9]">{{ $announcement->created_at->diffForHumans() }}</p>
                                </div>
                                @if($announcement->title)
                                    <p class="mt-1 text-sm font-bold text-[#101114] truncate">{{ $announcement->title }}</p>
                                @endif
                                <p class="mt-0.5 line-clamp-2 text-xs text-[#686b82]">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 120) }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <x-empty-state-inline title="กระดานสนทนา" body="ยังไม่มีประกาศ" />
                @endforelse
            </div>
        </div>

        {{-- ต้องดูแล --}}
        <div class="border-t border-[#f2eff5] p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="mt-1 text-xl font-black text-[#101114]">{{ 'งานในชั้นเรียนที่ต้องดูแล' }}</h2>
                </div>
                <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'pending']) }}" wire:navigate
                    class="text-sm font-bold text-(--cw-color) hover:opacity-80 hover:underline">{{ 'ดูทั้งหมด' }}</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($pendingAssignments->take(4) as $assignment)
                    <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}" wire:navigate
                        class="block rounded-[10px] border border-[#dedee5] px-3 py-3 transition hover:border-(--cw-color)/30 hover:bg-(--cw-faint)">
                        <p class="truncate text-sm font-extrabold text-[#101114]">{{ $assignment->title }}</p>
                        <p class="mt-1 text-xs font-semibold text-[#9497a9]">
                            {{ $assignment->due_date ? 'กำหนดส่ง ' . $assignment->due_date->translatedFormat('j M Y H:i') : 'ไม่มีกำหนด' }}
                        </p>
                    </a>
                @empty
                    <x-empty-state-inline title="ไม่มีงานที่รอดำเนินการ" body="ไม่มีอะไรที่ต้องดูแลตอนนี้" />
                @endforelse
            </div>
        </div>
    </section>
</div>
