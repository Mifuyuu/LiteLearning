@section('page-title', $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ \Illuminate\Support\Str::limit($classroom->name, 30) }}</span>
    </nav>
@endsection

@php
    $manager = $classroom->canManageClassroom(auth()->user());
    $themeColor = $classroom->themeCategory?->color ?? '#2563eb';
@endphp

<div class="space-y-5 ">
    <section class="overflow-hidden rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="h-2 w-full" style="background-color: {{ $themeColor }};"></div>
        <div class="p-5 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ 'หน้าหลัก' }}</p>
                    <h1 class="mt-2 text-2xl font-black leading-tight text-[#101114] sm:text-3xl">{{ $classroom->name }}</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-[#686b82]">
                        {{ $classroom->description ?: 'ภาพรวมอย่างรวดเร็วของห้องเรียนนี้' }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 rounded-[8px] bg-[rgba(37,99,235,0.12)] px-3 py-1.5 text-[var(--ll-blue)]">
                            <x-icon name="user" class="h-3.5 w-3.5" />
                            {{ $classroom->teacher->name }}
                        </span>
                        @if($classroom->section)
                            <span class="inline-flex items-center gap-1.5 rounded-[8px] bg-[rgba(104,107,130,0.12)] px-3 py-1.5 text-[#484b5e]">
                                <x-icon name="bookmark" class="h-3.5 w-3.5" />
                                {{ $classroom->section }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 rounded-[8px] bg-[rgba(104,107,130,0.12)] px-3 py-1.5 text-[#484b5e]">
                            <x-icon name="users" class="h-3.5 w-3.5" />
                            {{ $students->count() }} {{ 'นักเรียน' }}
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 flex-wrap gap-2">
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
            </div>
        </div>
    </section>

    <div class="grid gap-5 lg:grid-cols-3">
        <a href="{{ route('classroom.stream', $classroom) }}" wire:navigate
            class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition hover:border-[rgba(37,99,235,0.3)] hover:bg-[rgba(37,99,235,0.03)]">
            <div class="flex items-center justify-between gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-[10px] bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)]">
                    <x-icon name="chat-bubble-left-ellipsis" class="h-5 w-5" />
                </span>
                <span class="text-2xl font-black text-[#101114]">{{ $classroom->announcements->count() }}</span>
            </div>
            <h2 class="mt-4 text-lg font-black text-[#101114]">{{ 'กระดานสนทนา' }}</h2>
            <p class="mt-1 text-sm leading-6 text-[#686b82]">{{ 'ประกาศและความคิดเห็นในชั้นเรียน' }}</p>
        </a>

        <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
            class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition hover:border-[rgba(37,99,235,0.3)] hover:bg-[rgba(37,99,235,0.03)]">
            <div class="flex items-center justify-between gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-[10px] bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)]">
                    <x-icon name="clipboard-document-list" class="h-5 w-5" />
                </span>
                <span class="text-2xl font-black text-[#101114]">{{ $assignmentCount }}</span>
            </div>
            <h2 class="mt-4 text-lg font-black text-[#101114]">{{ 'งานในชั้นเรียน' }}</h2>
            <p class="mt-1 text-sm leading-6 text-[#686b82]">{{ 'งานที่มอบหมาย, งานที่รอทำ, และงานที่ทำเสร็จแล้ว' }}</p>
        </a>

        <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-last-name']) }}" wire:navigate
            class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition hover:border-[rgba(37,99,235,0.3)] hover:bg-[rgba(37,99,235,0.03)]">
            <div class="flex items-center justify-between gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-[10px] bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)]">
                    <x-icon name="users" class="h-5 w-5" />
                </span>
                <span class="text-2xl font-black text-[#101114]">{{ $students->count() }}</span>
            </div>
            <h2 class="mt-4 text-lg font-black text-[#101114]">{{ 'สมาชิก' }}</h2>
            <p class="mt-1 text-sm leading-6 text-[#686b82]">{{ 'ครู, ผู้สอนร่วม และนักเรียน' }}</p>
        </a>
    </div>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_340px]">
        <section class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ 'งานในชั้นเรียน' }}</p>
                    <h2 class="mt-1 text-xl font-black text-[#101114]">{{ 'ต้องดูแล' }}</h2>
                </div>
                <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'pending']) }}" wire:navigate
                    class="text-sm font-bold text-[var(--ll-blue)] hover:text-[var(--ll-blue-dark)]">{{ 'ดูทั้งหมด' }}</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($pendingAssignments->take(4) as $assignment)
                    <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}" wire:navigate
                        class="block rounded-[10px] border border-[#dedee5] px-3 py-3 transition hover:border-[rgba(37,99,235,0.3)] hover:bg-[var(--ll-blue-faint)]">
                        <p class="truncate text-sm font-extrabold text-[#101114]">{{ $assignment->title }}</p>
                        <p class="mt-1 text-xs font-semibold text-[#9497a9]">
                            {{ $assignment->due_date ? 'กำหนดส่ง ' . $assignment->due_date->format('d M Y H:i') : 'ไม่มีกำหนด' }}
                        </p>
                    </a>
                @empty
                    <x-empty-state-inline title="ไม่มีงานที่รอดำเนินการ" body="ไม่มีอะไรที่ต้องดูแลตอนนี้" />
                @endforelse
            </div>
        </section>

        @if($manager)
            <aside class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ 'การเข้าถึงชั้นเรียน' }}</p>
                <h2 class="mt-1 text-lg font-black text-[#101114]">{{ 'รหัสเข้าร่วม' }}</h2>
                <div class="mt-4 flex items-center justify-between gap-3 rounded-[10px] border border-[#dedee5] bg-[var(--ll-blue-faint)] px-4 py-3">
                    <p class="text-2xl font-black tracking-[0.22em] text-[#101114]">{{ $classroom->code }}</p>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $classroom->code }}')"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[9px] bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)] transition hover:bg-[rgba(37,99,235,0.24)]"
                        title="คัดลอก">
                        <x-icon name="clipboard-document-list" class="h-4 w-4" />
                    </button>
                </div>
            </aside>
        @endif
    </div>
</div>
