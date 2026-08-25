@section('page-title', 'สมุดเกรด - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            ชั้นเรียนของฉัน
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">
            {{ \Illuminate\Support\Str::limit($classroom->name, 15, '..') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">สมุดเกรด</span>
    </nav>
@endsection

@php
    $inputClass = 'rounded-lg border border-[#dedee5] bg-white px-4 py-2.5 text-sm text-[#101114] outline-none transition focus:border-(--ll-blue) focus:ring-2 focus:ring-[rgba(37,99,235,0.12)]';
@endphp

<div class="max-w-4xl mx-auto space-y-4">
    <section class="rounded-lg border-3 border-[#dedee5] bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">มุมมองครูผู้สอน</p>
                <h1 class="mt-2 text-2xl font-black leading-tight text-[#101114] sm:text-3xl">{{ $classroom->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#686b82]">
                    ดูคะแนนแบบรวมของทั้งห้อง พร้อมค้นหา กรอง ส่งออก และสลับรูปแบบการแสดงผลได้จากหน้านี้
                </p>
            </div>

        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-[#dedee5] bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#9497a9]">งานที่มอบหมาย</p>
            <p class="mt-2 text-2xl font-bold text-[#101114]">{{ $stats['assignment_count'] }}</p>
        </div>
        <div class="rounded-lg border border-[#dedee5] bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#9497a9]">อัตราการส่งงาน</p>
            <p class="mt-2 text-2xl font-bold text-[#101114]">{{ $stats['submission_rate'] }}%</p>
        </div>
        <div class="rounded-lg border border-[#dedee5] bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#9497a9]">คะแนนเฉลี่ย</p>
            <p class="mt-2 text-2xl font-bold text-[#101114]">{{ $stats['avg_score'] !== null ? $stats['avg_score'].'%' : '—' }}</p>
        </div>
        <div class="rounded-lg border border-[#dedee5] bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#9497a9]">รอตรวจ</p>
            <p class="mt-2 text-2xl font-bold text-[#101114]">{{ $stats['pending_grading'] }}</p>
        </div>
    </section>

    <section class="flex flex-wrap items-center gap-3 rounded-lg border-3 border-[#dedee5] bg-white p-3">
        <div class="relative min-w-56 flex-1">
            <x-icon name="magnifying-glass" class="h-4 w-4 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#9497a9]" />
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="ค้นหานักเรียน..."
                class="{{ $inputClass }} w-full pl-10">
        </div>

        @if($topics->isNotEmpty())
            <select wire:model.live="filterTopic" class="{{ $inputClass }}">
                <option value="">ทุกหัวข้อ</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic }}">{{ $topic }}</option>
                @endforeach
            </select>
        @endif

        <select wire:model.live="filterType" class="{{ $inputClass }}">
            <option value="">ทุกประเภท</option>
            @foreach($types as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
            @endforeach
        </select>

        <button type="button" wire:click="exportCsv"
            class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-(--ll-blue) px-4 py-2.5 text-sm font-bold text-white transition hover:bg-(--ll-blue-dark)">
            <x-icon name="arrow-down-tray" class="h-4 w-4" />
            ส่งออก CSV
        </button>
    </section>

    <section class="overflow-hidden rounded-lg border-3 border-[#dedee5] bg-white">
        @if($students->isEmpty())
            <div class="p-12">
                <x-empty-state-inline :title="'สมุดเกรด'" :body="'ไม่มีนักเรียนที่ตรงกับเงื่อนไขที่กรอง'" />
            </div>
        @elseif($assignments->isEmpty())
            <div class="p-12">
                <x-empty-state-inline :title="'สมุดเกรด'" :body="'ไม่มีงานที่สามารถให้คะแนนได้สำหรับเงื่อนไขที่กรอง'" />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#f7f7fa]">
                        <tr class="border-b border-[#dedee5]">
                            <th class="sticky left-0 z-10 min-w-56 bg-[#f7f7fa] px-4 py-3 text-left font-semibold text-[#101114]">
                                นักเรียน
                            </th>
                            @foreach($assignments as $assignment)
                                <th class="px-3 py-3 min-w-32 border-l border-[#ece9f0] text-center font-semibold text-[#686b82]">
                                    <div class="truncate" title="{{ $assignment->title }}">{{ $assignment->title }}</div>
                                    <div class="mt-1 text-xs font-normal text-[#9497a9]">/{{ $assignment->max_score }}</div>
                                </th>
                            @endforeach
                            <th class="px-3 py-3 min-w-28 border-l border-[#dedee5] text-center font-semibold text-[#101114]">รวม</th>
                            <th class="px-3 py-3 min-w-28 text-center font-semibold text-[#101114]">เฉลี่ย</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ece9f0]">
                        @foreach($students as $student)
                            @php $summary = $summaries[$student->id]; @endphp
                            <tr class="transition-colors hover:bg-[rgba(37,99,235,0.03)]">
                                <td class="sticky left-0 z-10 bg-white px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="h-10 w-10 rounded-xl object-cover">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-[#101114]">{{ $student->name }}</p>
                                            <p class="truncate text-xs text-[#9497a9]">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                @foreach($assignments as $assignment)
                                    @php
                                        $submission = $scoreMap[$student->id][$assignment->id] ?? null;
                                        $maxScore = $assignment->max_score ?: 0;
                                    @endphp
                                    <td class="px-3 py-3 border-l border-[#ece9f0] text-center">
                                        @if($submission && $submission->isGraded())
                                            <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $submission]) }}" wire:navigate
                                                class="inline-flex items-center gap-1 rounded-[7px] bg-[rgba(37,99,235,0.12)] px-2.5 py-1 text-xs font-bold text-(--ll-blue) transition hover:bg-[rgba(37,99,235,0.2)]">
                                                {{ $submission->score }}/{{ $maxScore }}
                                            </a>
                                        @elseif($submission && $submission->isTurnedIn())
                                            <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $submission]) }}" wire:navigate
                                                class="inline-flex items-center gap-1 rounded-[7px] bg-blue-50 px-2.5 py-1 text-xs font-bold text-(--ll-blue) transition hover:bg-blue-100">
                                                <x-icon name="clock" class="h-3 w-3" />
                                                รอตรวจ
                                            </a>
                                        @elseif($submission && $submission->status === 'returned')
                                            <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $submission]) }}" wire:navigate
                                                class="inline-flex items-center gap-1 rounded-[7px] bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                                                <x-icon name="arrow-uturn-left" class="h-3 w-3" />
                                                ส่งคืน
                                            </a>
                                        @else
                                            <span class="text-[#c9cbd6]">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="px-3 py-3 border-l border-[#dedee5] text-center">
                                    @if($summary['maxPossible'] > 0)
                                        <span class="font-semibold text-[#101114]">{{ $summary['totalScore'] }}/{{ $summary['maxPossible'] }}</span>
                                    @else
                                        <span class="text-[#c9cbd6]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($summary['avgPercent'] !== null)
                                        <span class="inline-flex items-center rounded-[7px] bg-[rgba(37,99,235,0.12)] px-2.5 py-1 text-xs font-bold text-(--ll-blue)">
                                            {{ $summary['avgPercent'] }}%
                                        </span>
                                    @else
                                        <span class="text-[#c9cbd6]">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
