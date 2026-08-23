@section('page-title', 'งานในชั้นเรียน' . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ $classroom->name }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'งานในชั้นเรียน' }}</span>
    </nav>
@endsection

@php
    $scopeLinks = [
        'all' => 'ทั้งหมด',
        'pending' => auth()->user()->isStudent() ? 'ยังไม่ทำ' : 'กำลังทำ',
        'completed' => auth()->user()->isStudent() ? 'เสร็จแล้ว' : 'เสร็จสมบูรณ์',
    ];

    $renderSection = function (\Illuminate\Support\Collection $items, string $title, string $emptyText) use ($classroom) {
        if ($items->isEmpty()) {
            return view('components.empty-state-inline', [
                'title' => $title,
                'body' => $emptyText,
            ])->render();
        }

        $grouped = $items->groupBy(fn ($assignment) => $assignment->topic ?? 'ทั่วไป');

        ob_start();
        echo '<section class="space-y-5">';
        echo '<p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9497a9]">'.e($title).'</p>';
        foreach ($grouped as $topic => $assignments) {
            echo '<section class="space-y-3">';
            echo '<div class="flex items-center gap-3">';
            echo '<h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9497a9]">'.e($topic).'</h3>';
            echo '<div class="h-px flex-1 bg-[#dedee5]"></div>';
            echo '</div>';
            echo '<div class="space-y-3">';
            foreach ($assignments as $assignment) {
                $submission = auth()->user()->isStudent()
                    ? $assignment->submissions->firstWhere('user_id', auth()->id())
                    : null;
                $isCompleted = $submission?->isTurnedIn() ?? false;
                echo view('livewire.classroom.work-item-card', [
                    'assignment' => $assignment,
                    'classroom' => $classroom,
                    'submission' => $submission,
                    'isCompleted' => $isCompleted,
                ])->render();
            }
            echo '</div>';
            echo '</section>';
        }
        echo '</section>';

        return ob_get_clean();
    };
@endphp

<div class="max-w-4xl mx-auto">
    <section class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)]">
        <div class="flex flex-wrap items-center gap-2 p-4">
        @foreach($scopeLinks as $key => $label)
            <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => $key]) }}" wire:navigate
                class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $scope === $key ? 'bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)]' : 'text-[#686b82] hover:bg-[var(--ll-blue-faint)] hover:text-[var(--ll-blue)]' }}">
                {{ $label }}
            </a>
        @endforeach
        @if($classroom->canManageClassroom(auth()->user()))
            <div class="ml-auto flex flex-wrap gap-2">
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] bg-[var(--ll-blue)] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[var(--ll-blue-dark)]">
                        <x-icon name="plus" class="h-4 w-4" />
                        {{ 'สร้าง' }}
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 transition-transform" ::class="open ? 'rotate-180' : ''" />
                    </button>
                    <ul x-show="open" x-cloak
                        class="absolute menu right-0 top-full z-50 mt-2 w-44 rounded-xl border border-[#dedee5] bg-white p-1.5 shadow-lg">
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=file" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-[8px] px-3 py-2 text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="arrow-up-tray" class="h-4 w-4 shrink-0" />
                                {{ 'งานส่งไฟล์' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=announcement" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-[8px] px-3 py-2 text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="megaphone" class="h-4 w-4 shrink-0" />
                                {{ 'ประกาศ' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('material.create', $classroom) }}" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-[8px] px-3 py-2 text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="book-open" class="h-4 w-4 shrink-0" />
                                {{ 'สื่อการสอน' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignment.create', $classroom) }}?type=attendance" wire:navigate @click="open = false" class="flex items-center gap-2.5 rounded-[8px] px-3 py-2 text-sm font-medium text-[#101114] hover:bg-[rgba(37,99,235,0.06)] hover:text-[var(--ll-blue)]">
                                <x-icon name="check-circle" class="h-4 w-4 shrink-0" />
                                {{ 'งานเช็คชื่อ' }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
        </div>

        <div class="border-t border-[#dedee5] mx-6"></div>

        <div class="p-6 space-y-6">
            @if($scope !== 'completed')
                {!! $renderSection($pendingAssignments, $scopeLinks['pending'], 'ไม่มีงานในสถานะนี้') !!}
            @endif
            @if($scope === 'all')
                <div class="border-t border-[#dedee5]"></div>
            @endif
            @if($scope !== 'pending')
                {!! $renderSection($completedAssignments, $scopeLinks['completed'], 'ไม่มีงานในสถานะนี้') !!}
            @endif
        </div>
    </section>
</div>
