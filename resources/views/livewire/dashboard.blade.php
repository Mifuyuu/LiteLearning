@section('page-title', 'แดชบอร์ด')

@php
    $isStudent = $role === 'student';
    $displayName = trim(explode(' ', $user->name)[0] ?? $user->name);
    $activityDays = collect($activity['days']);
    $activityGridStart = \Illuminate\Support\Carbon::parse($activity['grid_start_date']);
    $monthLabels = $activity['month_labels'];
     $activityLevelClasses = [
         0 => 'bg-[#ece9f0]',
         1 => 'bg-[#dbeafe]',
         2 => 'bg-[#93c5fd]',
         3 => 'bg-[#60a5fa]',
         4 => 'bg-[#3b82f6]',
     ];
@endphp

<div data-dashboard-role="{{ $role }}"
    class="flex min-h-full flex-col gap-3  xl:h-[calc(100vh-3rem)] xl:min-h-[560px] xl:overflow-hidden">
    <style>
        /* Mobile (default) */
        .activity-grid-cols {
            grid-template-columns: repeat({{ $activity['week_count'] }}, 0.5rem) !important;
            gap: 1px !important;
        }
        .activity-cell {
            width: 0.5rem !important;
            height: 0.5rem !important;
            border-radius: 0px !important;
        }

        /* Tablets & Medium screens */
        @media (min-width: 640px) {
            .activity-grid-cols {
                grid-template-columns: repeat({{ $activity['week_count'] }}, 0.75rem) !important;
                gap: 0.25rem !important;
            }
            .activity-cell {
                width: 0.75rem !important;
                height: 0.75rem !important;
                border-radius: 3px !important;
            }
        }

        /* Large screens (desktop) */
        @media (min-width: 1280px) {
            .activity-grid-cols {
                grid-template-columns: repeat({{ $activity['week_count'] }}, minmax(0, 1fr)) !important;
                gap: 0.25rem !important;
            }
            .activity-cell {
                width: 100% !important;
                height: auto !important;
                border-radius: 3px !important;
            }
        }
    </style>
    <header class="flex shrink-0 items-center justify-between gap-4">
        <div class="min-w-0">
            <h1 class="truncate text-2xl font-bold tracking-tight text-[#101114]">
                สวัสดี, {{ $displayName }}
            </h1>
            <p class="mt-0.5 truncate text-sm text-[#686b82]">
                {{ $isStudent ? 'ดูว่าการเรียนรู้ของคุณกำลังเติบโตอย่างไรในวันนี้' : 'เริ่มต้นจากงานที่ต้องตรวจสอบ' }}
            </p>
        </div>
        <a href="{{ $isStudent ? route('calendar') : route('to-review') }}" wire:navigate
            class="inline-flex shrink-0 items-center gap-2 rounded-[10px] bg-white px-3 py-2 text-sm font-semibold text-[var(--ll-blue)] transition hover:bg-[var(--ll-blue-hover)]">
            <x-icon name="calendar-days" class="h-4 w-4" />
            <span class="hidden sm:inline">{{ $isStudent ? 'ปฏิทิน' : 'รอตรวจ' }}</span>
        </a>
    </header>

    <div class="grid min-h-0 flex-1 gap-3 xl:grid-cols-[minmax(220px,0.78fr)_minmax(380px,1.55fr)_minmax(220px,0.78fr)]">
        <div class="grid min-h-0 gap-3 xl:grid-rows-[180px_minmax(0,1fr)]">
            <section class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body gap-0 p-5">
                    @if($isStudent)
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[var(--ll-blue)]">เลเวลปัจจุบัน</p>
                                <p class="mt-2 text-5xl font-bold leading-none text-[#101114]">{{ $primaryMetric['level'] }}</p>
                            </div>
                            <span class="rounded-[9px] bg-[var(--ll-blue-subtle)] px-2.5 py-1 text-xs font-bold text-[var(--ll-blue)]">
                                {{ number_format($primaryMetric['xp_current']) }} / {{ number_format($primaryMetric['xp_required']) }} XP
                            </span>
                        </div>
                        <div class="dashboard-liquid-progress mt-5 outline-2 outline-[rgba(37,99,235,0.28)]" role="progressbar"
                            aria-label="ความคืบหน้าเลเวล" aria-valuemin="0" aria-valuemax="100"
                            aria-valuenow="{{ $primaryMetric['progress_percent'] }}">
                            <span class="dashboard-liquid-fill" style="width: {{ $primaryMetric['progress_percent'] }}%"></span>
                        </div>
                        <p class="mt-2 text-xs text-[#686b82]">
                            อีก {{ number_format($primaryMetric['remaining']) }} XP จะถึงเลเวล {{ $primaryMetric['level'] + 1 }}
                        </p>
                    @else
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[var(--ll-blue)]">รอตรวจ</p>
                                <p class="mt-2 text-5xl font-bold leading-none text-[#101114]">{{ number_format($primaryMetric['pending']) }}</p>
                            </div>
                            <span class="rounded-[9px] bg-[var(--ll-blue-subtle)] px-2.5 py-1 text-xs font-bold text-[var(--ll-blue)]">
                                {{ number_format($primaryMetric['graded_this_week']) }} ตรวจแล้ว
                            </span>
                        </div>
                        <div class="dashboard-liquid-progress mt-5 outline-2 outline-[rgba(37,99,235,0.28)]" role="progressbar"
                            aria-label="ความคืบหน้าการตรวจ" aria-valuemin="0" aria-valuemax="100"
                            aria-valuenow="{{ $primaryMetric['progress_percent'] }}">
                            <span class="dashboard-liquid-fill" style="width: {{ $primaryMetric['progress_percent'] }}%"></span>
                        </div>
                        <p class="mt-2 text-xs text-[#686b82]">การตรวจงานประจำสัปดาห์ · {{ $primaryMetric['progress_percent'] }}%</p>
                    @endif
                </div>
            </section>

            <section class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body min-h-0 gap-0 p-4">
                    <div class="flex shrink-0 items-center justify-between gap-3">
                        <h2 class="card-title text-base text-[#101114]">{{ $isStudent ? 'กำลังจะถึง' : 'คิวตรวจงาน' }}</h2>
                        <a href="{{ $isStudent ? route('calendar') : route('to-review') }}" wire:navigate
                            class="text-xs font-bold text-[var(--ll-blue)] hover:text-[var(--ll-blue-dark)]">ดูทั้งหมด</a>
                    </div>
                    <div class="mt-2 min-h-0 divide-y divide-[#ece9f0] overflow-hidden">
                        @forelse($actionItems as $item)
                            <a href="{{ $isStudent ? route('assignment.show', [$item->classroom, $item]) : route('to-review') }}"
                                wire:navigate class="group flex items-center gap-3 py-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] text-white"
                                    style="background-color: {{ $item->classroom?->themeCategory?->color ?? '#2563eb' }}">
                                    <x-icon name="{{ $isStudent ? 'document-text' : 'clipboard-document-list' }}" class="h-4 w-4" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-[#101114] group-hover:text-[var(--ll-blue)]">{{ $item->title }}</span>
                                    <span class="mt-0.5 block truncate text-xs text-[#686b82]">{{ $item->classroom?->name }}</span>
                                </span>
                                <span class="shrink-0 rounded-[7px] bg-[rgba(37,99,235,0.12)] px-2 py-1 text-[11px] font-bold text-[var(--ll-blue)]">
                                    {{ $isStudent ? $item->due_date?->translatedFormat('j M') : $item->pending_count }}
                                </span>
                            </a>
                        @empty
                            <div class="flex h-full min-h-48 flex-col items-center justify-center text-center">
                                <span class="flex h-11 w-11 items-center justify-center rounded-[12px] bg-[rgba(37,99,235,0.12)] text-[var(--ll-blue)]">
                                    <x-icon name="check-circle" class="h-5 w-5" />
                                </span>
                                <p class="mt-3 text-sm font-semibold text-[#101114]">ไม่มีอะไรเร่งด่วนตอนนี้</p>
                                <p class="mt-1 text-xs text-[#9497a9]">{{ $isStudent ? 'งานที่กำลังจะถึงจะแสดงที่นี่' : 'งานที่ส่งใหม่จะแสดงที่นี่' }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <section data-activity-heatmap
            class="card card-border border-[#dedee5] bg-white">
            <div class="card-body flex h-full min-h-0 flex-col gap-0 p-4 sm:p-5">
                <div class="flex shrink-0 items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[var(--ll-blue)]">กิจกรรมในรอบ 6 เดือน</p>
                        <h2 class="card-title mt-1 text-lg text-[#101114]">
                            {{ $isStudent ? 'ความสม่ำเสมอในการเรียนรู้' : 'กิจกรรมการสอน' }}
                        </h2>
                        <p class="mt-1 text-xs text-[#686b82]">
                            {{ $isStudent ? 'การส่งงาน, การเข้าเรียน, ความคิดเห็น และคะแนน' : 'งานในชั้นเรียน, การตรวจงาน และความคิดเห็น' }}
                        </p>
                    </div>
                    <span class="rounded-[9px] bg-[rgba(37,99,235,0.12)] px-2.5 py-1.5 text-xs font-bold text-[var(--ll-blue)]">
                        {{ number_format($activity['total']) }} กิจกรรม
                    </span>
                </div>
                 <div class="mt-5 min-h-0 flex-1 overflow-visible">
                    <div class="grid xl:min-w-0 w-max xl:w-full grid-cols-[24px_max-content] xl:grid-cols-[24px_minmax(0,1fr)] gap-2">
                        <span></span>
                        <div class="activity-grid-cols grid text-[10px] font-medium text-[#9497a9]">
                            @foreach($monthLabels as $month)
                                <span class="whitespace-nowrap" style="grid-column: {{ $month['week'] }};">{{ $month['label'] }}</span>
                            @endforeach
                        </div>
                        <div class="grid grid-rows-7 gap-1 text-[9px] text-[#9497a9]">
                            <span>จ.</span><span></span><span>พ.</span><span></span>
                            <span>ศ.</span><span></span><span>อา.</span>
                        </div>
                        <div class="activity-grid-cols grid grid-flow-col grid-rows-7 gap-1">
                            @foreach($activityDays as $day)
                                <span data-activity-cell tabindex="0"
                                    class="activity-cell tooltip tooltip-top aspect-square size-3 rounded-[3px] outline-none ring-[var(--ll-blue)] transition hover:ring-2 focus:ring-2 {{ ! $day['is_in_year'] || $day['is_future'] ? 'bg-[#f5f3f7]' : $activityLevelClasses[$day['level']] }}"
                                    data-tip="{{ $day['label'] }} · {{ $day['count'] }} กิจกรรม"
                                    aria-label="{{ $day['label'] }}: {{ $day['count'] }} กิจกรรม"></span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid shrink-0 grid-cols-3 gap-2">
                    @foreach($activitySummaries as $summary)
                        <div class="rounded-[11px] bg-[#f7f5f9] p-3">
                            <p class="text-lg font-bold leading-none text-[#101114]">{{ $summary['value'] }}</p>
                            <p class="mt-1 truncate text-[10px] text-[#686b82]">{{ $summary['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="grid min-h-0 gap-3 xl:grid-rows-[340px_minmax(0,1fr)]">
            <a href="{{ route('profile') }}" wire:navigate
                class="card card-border group overflow-hidden border-[#dedee5] bg-white transition hover:border-[rgba(37,99,235,0.3)]">
                <div class="card-body items-center justify-center gap-0 p-5 text-center">
                    <div class="avatar">
                            <div class="h-20 w-20 rounded-full">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="object-cover">
                        </div>
                    </div>
                    <h2 class="mt-4 max-w-full truncate text-lg font-bold text-[#101114] group-hover:text-[var(--ll-blue)]">{{ $user->name }}</h2>
                    <p class="mt-1 text-xs text-[#686b82]">{{ $isStudent ? 'นักเรียน' : 'ครู' }} · {{ $classrooms->count() }} ห้องเรียน</p>
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        @if($isStudent)
                            <span class="badge border-0 bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)]">เลเวล {{ $primaryMetric['level'] }}</span>
                        @else
                            <span class="badge border-0 bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)]">ครู</span>
                        @endif
                        <span class="badge border-0 bg-[#f2eff5] text-[#686b82]">ดูโปรไฟล์</span>
                    </div>
                </div>
            </a>

            <section class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body gap-0 p-4">
                    <h2 class="card-title text-base text-[#101114]">สถิติด่วน</h2>
                    <div class="mt-3 grid min-h-0 flex-1 grid-cols-2 gap-2">
                        @foreach($quickStats as $stat)
                            <div class="flex min-h-20 flex-col justify-between rounded-[11px] bg-[#f7f5f9] p-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-[9px] bg-[rgba(37,99,235,0.14)] text-[var(--ll-blue)]">
                                    <x-icon :name="$stat['icon']" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="truncate text-xl font-bold leading-none text-[#101114]">{{ $stat['value'] }}</p>
                                    <p class="mt-1 truncate text-[10px] text-[#686b82]">{{ $stat['label'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
