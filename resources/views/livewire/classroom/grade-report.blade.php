@section('page-title', 'สมุดเกรด - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-slate-500 hover:text-slate-900 transition-colors">
            ชั้นเรียนของฉัน
        </a>
        <x-icon name="chevron-right" class="h-4 w-4 text-slate-300" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-slate-500 hover:text-slate-900 transition-colors">
            {{ \Illuminate\Support\Str::limit($classroom->name, 30) }}
        </a>
        <x-icon name="chevron-right" class="h-4 w-4 text-slate-300" />
        <span class="text-slate-900 font-semibold">สมุดเกรด</span>
    </nav>
@endsection

@php
    $cellPadding = $display === 'compact' ? 'px-2 py-2' : 'px-3 py-3';
@endphp

<div class="space-y-6 max-w-4xl mx-auto">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(168,85,247,0.18),_transparent_30%),linear-gradient(135deg,#ffffff_0%,#faf5ff_42%,#eef2ff_100%)] p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-green-700">
                    <x-icon name="chart-bar" class="h-4 w-4" />
                    มุมมองครูผู้สอน
                </span>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">{{ $classroom->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    ดูคะแนนแบบรวมของทั้งห้อง พร้อมค้นหา กรอง ส่งออก และสลับรูปแบบการแสดงผลได้จากหน้านี้
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('classroom.gradebook', ['classroom' => $classroom, 'sort' => 'sort-first-name', 'display' => $display]) }}" wire:navigate
                    class="rounded-xl px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-first-name' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    ชื่อต้น
                </a>
                <a href="{{ route('classroom.gradebook', ['classroom' => $classroom, 'sort' => 'sort-last-name', 'display' => $display]) }}" wire:navigate
                    class="rounded-xl px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-last-name' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    นามสกุล
                </a>
                <a href="{{ route('classroom.gradebook', ['classroom' => $classroom, 'sort' => 'sort-last-name', 'display' => 'default']) }}" wire:navigate
                    class="rounded-xl px-4 py-2 text-sm font-medium transition-colors {{ $display === 'default' ? 'bg-green-100 text-green-700' : 'bg-white text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    ปกติ
                </a>
                <a href="{{ route('classroom.gradebook', ['classroom' => $classroom, 'sort' => 'sort-last-name', 'display' => 'compact']) }}" wire:navigate
                    class="rounded-xl px-4 py-2 text-sm font-medium transition-colors {{ $display === 'compact' ? 'bg-green-100 text-green-700' : 'bg-white text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    กระทัดรัด
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">งานที่มอบหมาย</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['assignment_count'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">อัตราการส่งงาน</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['submission_rate'] }}%</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">คะแนนเฉลี่ย</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['avg_score'] !== null ? $stats['avg_score'].'%' : '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">รอตรวจ</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['pending_grading'] }}</p>
        </div>
    </section>

    <section class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="relative min-w-56 flex-1">
            <x-icon name="magnifying-glass" class="h-4 w-4 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="ค้นหานักเรียน..."
                class="w-full rounded-2xl border border-slate-300 px-10 py-3 text-sm outline-none transition focus:border-green-400 focus:ring-2 focus:ring-green-100">
        </div>

        @if($topics->isNotEmpty())
            <select wire:model.live="filterTopic"
                class="rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-green-400 focus:ring-2 focus:ring-green-100">
                <option value="">ทุกหัวข้อ</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic }}">{{ $topic }}</option>
                @endforeach
            </select>
        @endif

        <select wire:model.live="filterType"
            class="rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-green-400 focus:ring-2 focus:ring-green-100">
            <option value="">ทุกประเภท</option>
            @foreach($types as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
            @endforeach
        </select>

        <button type="button" wire:click="exportCsv"
            class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
            <x-icon name="arrow-down-tray" class="h-4 w-4" />
            ส่งออก CSV
        </button>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
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
                    <thead class="bg-slate-50">
                        <tr class="border-b border-slate-200">
                            <th class="sticky left-0 z-10 min-w-56 bg-slate-50 px-4 py-3 text-left font-semibold text-slate-700">
                                นักเรียน
                            </th>
                            @foreach($assignments as $assignment)
                                <th class="{{ $cellPadding }} min-w-32 border-l border-slate-100 text-center font-semibold text-slate-600">
                                    <div class="truncate" title="{{ $assignment->title }}">{{ $assignment->title }}</div>
                                    <div class="mt-1 text-xs font-normal text-slate-400">/{{ $assignment->max_score }}</div>
                                </th>
                            @endforeach
                            <th class="{{ $cellPadding }} min-w-28 border-l border-slate-200 text-center font-semibold text-slate-700">รวม</th>
                            <th class="{{ $cellPadding }} min-w-28 text-center font-semibold text-slate-700">เฉลี่ย</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $student)
                            @php $summary = $summaries[$student->id]; @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="sticky left-0 z-10 bg-white px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="h-10 w-10 rounded-2xl object-cover">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-900">{{ $student->name }}</p>
                                            <p class="truncate text-xs text-slate-500">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                @foreach($assignments as $assignment)
                                    @php
                                        $submission = $scoreMap[$student->id][$assignment->id] ?? null;
                                        $maxScore = $assignment->max_score ?: 0;
                                    @endphp
                                    <td class="{{ $cellPadding }} border-l border-slate-100 text-center">
                                        @if($submission && $submission->isGraded())
                                            <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $submission]) }}" wire:navigate
                                                class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                                {{ $submission->score }}/{{ $maxScore }}
                                            </a>
                                        @elseif($submission && $submission->isTurnedIn())
                                            <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $submission]) }}" wire:navigate
                                                class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                                <x-icon name="clock" class="h-3 w-3" />
                                                รอตรวจ
                                            </a>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="{{ $cellPadding }} border-l border-slate-200 text-center">
                                    @if($summary['maxPossible'] > 0)
                                        <span class="font-semibold text-slate-900">{{ $summary['totalScore'] }}/{{ $summary['maxPossible'] }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="{{ $cellPadding }} text-center">
                                    @if($summary['avgPercent'] !== null)
                                        <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                            {{ $summary['avgPercent'] }}%
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
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
