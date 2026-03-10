@section('page-title', $classroom->name . ' — ' . __('Grade Report'))
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('ชั้นเรียนของฉัน') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ Illuminate\Support\Str::limit($classroom->name, 30) }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold">{{ __('Grade Report') }}</span>
    </nav>
@endsection

<div style="zoom: {{ auth()->user()->ui_scale }}%;" class="animate__animated animate__fadeIn">

    {{-- Header --}}
    <div class="rounded-2xl overflow-hidden mb-6 relative"
        style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}">
        <div class="absolute inset-0 bg-linear-to-b from-black/10 to-black/40"></div>
        <div class="relative p-6 sm:p-8">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <p class="text-white/70 text-sm font-medium mb-1">
                        <i class="fas fa-chart-bar mr-1"></i> {{ __('Grade Report') }}
                    </p>
                    <h1 class="text-3xl font-bold text-white">{{ $classroom->name }}</h1>
                    <p class="text-white/80 mt-1">{{ $classroom->section }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('classroom.show', $classroom) }}"
                        class="btn-3d btn-3d--dark px-4 py-2 rounded-xl text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> {{ __('กลับห้องเรียน') }}
                    </a>
                    <button wire:click="exportCsv"
                        class="btn-3d btn-3d--white px-4 py-2 rounded-xl text-sm">
                        <i class="fas fa-download mr-1"></i> {{ __('Export CSV') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-tasks text-indigo-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['assignment_count'] }}</p>
                <p class="text-xs text-gray-500">{{ __('งานทั้งหมด') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-paper-plane text-green-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['submission_rate'] }}%</p>
                <p class="text-xs text-gray-500">{{ __('อัตราการส่ง') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-star text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">
                    {{ $stats['avg_score'] !== null ? $stats['avg_score'].'%' : '—' }}
                </p>
                <p class="text-xs text-gray-500">{{ __('คะแนนเฉลี่ย') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-hourglass-half text-amber-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_grading'] }}</p>
                <p class="text-xs text-gray-500">{{ __('รอให้คะแนน') }}</p>
            </div>
        </div>
    </div>

    {{-- Filters + Search --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4 flex flex-wrap items-center gap-3">
        {{-- Search --}}
        <div class="relative flex-1 min-w-48">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="{{ __('ค้นหานักเรียน...') }}"
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
        </div>

        {{-- Topic filter --}}
        @if($topics->isNotEmpty())
            <select wire:model.live="filterTopic"
                class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">{{ __('ทุก Topic') }}</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic }}">{{ $topic }}</option>
                @endforeach
            </select>
        @endif

        {{-- Type filter --}}
        <select wire:model.live="filterType"
            class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white">
            <option value="">{{ __('ทุกประเภท') }}</option>
            <option value="file">{{ __('ส่งไฟล์') }}</option>
            <option value="question">{{ __('ตอบคำถาม') }}</option>
            <option value="project">{{ __('โปรเจกต์') }}</option>
            <option value="attendance">{{ __('การเช็คชื่อ') }}</option>
        </select>

        {{-- Clear filters --}}
        @if($filterTopic || $filterType || $search)
            <button wire:click="$set('filterTopic', ''); $set('filterType', ''); $set('search', '')"
                class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                <i class="fas fa-times mr-1"></i> {{ __('ล้าง') }}
            </button>
        @endif
    </div>

    {{-- Grade table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($students->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <i class="fas fa-users text-4xl mb-3 block"></i>
                <p class="font-medium">{{ __('ยังไม่มีนักเรียนในห้องเรียนนี้') }}</p>
            </div>
        @elseif($assignments->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <i class="fas fa-tasks text-4xl mb-3 block"></i>
                <p class="font-medium">{{ __('ยังไม่มีงานที่ต้องส่งในห้องเรียนนี้') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            {{-- Sticky student column --}}
                            <th class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200 min-w-48 whitespace-nowrap">
                                {{ __('นักเรียน') }}
                                <span class="ml-1 text-gray-400 font-normal text-xs">({{ $students->count() }})</span>
                            </th>
                            {{-- Assignment columns --}}
                            @foreach($assignments as $assignment)
                                <th class="px-3 py-3 text-center font-medium text-gray-600 min-w-28 whitespace-nowrap border-r border-gray-100 last:border-r-0">
                                    <div class="truncate max-w-28" title="{{ $assignment->title }}">
                                        {{ Illuminate\Support\Str::limit($assignment->title, 18) }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-normal mt-0.5">/{{ $assignment->max_score }}</div>
                                </th>
                            @endforeach
                            {{-- Summary columns --}}
                            <th class="px-3 py-3 text-center font-semibold text-gray-700 min-w-24 whitespace-nowrap bg-gray-50 border-l border-gray-200">
                                {{ __('รวม') }}
                            </th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-700 min-w-24 whitespace-nowrap bg-gray-50">
                                {{ __('เฉลี่ย') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $student)
                            @php
                                $totalScore = 0;
                                $maxPossible = 0;
                                $gradedCount = 0;
                                foreach ($assignments as $a) {
                                    $sub = $scoreMap[$student->id][$a->id] ?? null;
                                    if ($sub && $sub->isGraded()) {
                                        $totalScore += $sub->score;
                                        $maxPossible += $a->max_score ?? 0;
                                        $gradedCount++;
                                    }
                                }
                                $avgPercent = $maxPossible > 0 ? round($totalScore / $maxPossible * 100) : null;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Sticky student name --}}
                                <td class="sticky left-0 z-10 bg-white hover:bg-gray-50 px-4 py-3 border-r border-gray-200">
                                    <div class="flex items-center gap-2.5">
                                        @if($student->avatar)
                                            <img src="{{ Storage::url($student->avatar) }}"
                                                class="w-7 h-7 rounded-full object-cover flex-shrink-0" />
                                        @else
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-indigo-600 text-xs font-bold">{{ mb_substr($student->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 truncate max-w-36" title="{{ $student->name }}">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-400 truncate max-w-36">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Score cells --}}
                                @foreach($assignments as $assignment)
                                    @php
                                        $sub = $scoreMap[$student->id][$assignment->id] ?? null;
                                        $maxScore = $assignment->max_score ?? 0;
                                        $pct = ($sub && $sub->isGraded() && $maxScore > 0)
                                            ? round($sub->score / $maxScore * 100)
                                            : null;
                                        $cellBg = '';
                                        $cellText = '';
                                        if ($pct !== null) {
                                            if ($pct >= 70) { $cellBg = 'bg-green-50'; $cellText = 'text-green-700'; }
                                            elseif ($pct >= 50) { $cellBg = 'bg-amber-50'; $cellText = 'text-amber-700'; }
                                            else { $cellBg = 'bg-red-50'; $cellText = 'text-red-700'; }
                                        }
                                    @endphp
                                    <td class="px-3 py-3 text-center border-r border-gray-100 last:border-r-0 {{ $cellBg }}">
                                        @if($sub && $sub->isGraded())
                                            <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $sub]) }}"
                                                class="inline-flex items-center gap-1 font-semibold {{ $cellText }} hover:underline">
                                                {{ $sub->score }}<span class="text-gray-400 font-normal text-xs">/{{ $maxScore }}</span>
                                            </a>
                                        @elseif($sub && $sub->isTurnedIn())
                                            <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $sub]) }}"
                                                class="inline-flex items-center gap-1 text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full hover:bg-amber-100 transition-colors">
                                                <i class="fas fa-clock text-xs"></i> {{ __('รอคะแนน') }}
                                            </a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Summary --}}
                                <td class="px-3 py-3 text-center border-l border-gray-200 bg-gray-50/50">
                                    @if($gradedCount > 0)
                                        <span class="font-semibold text-gray-700">{{ $totalScore }}<span class="text-gray-400 font-normal text-xs">/{{ $maxPossible }}</span></span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center bg-gray-50/50">
                                    @if($avgPercent !== null)
                                        @php
                                            $sumColor = $avgPercent >= 70 ? 'text-green-700 bg-green-100' : ($avgPercent >= 50 ? 'text-amber-700 bg-amber-100' : 'text-red-700 bg-red-100');
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $sumColor }}">{{ $avgPercent }}%</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
