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

<div data-dashboard-role="{{ $role }}" class="max-w-4xl mx-auto">
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

    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4 p-5 lg:p-7">
            <div class="min-w-0">
                <h1 class="truncate text-2xl font-bold tracking-tight text-[#101114] sm:text-3xl">
                    สวัสดี, {{ $displayName }}
                </h1>
                <p class="mt-1 truncate text-sm text-[#686b82]">
                    {{ $isStudent ? 'ดูว่าการเรียนรู้ของคุณกำลังเติบโตอย่างไรในวันนี้' : 'เริ่มต้นจากงานที่ต้องตรวจสอบ' }}
                </p>
            </div>
            <a href="{{ $isStudent ? route('calendar') : route('to-review') }}" wire:navigate
                class="inline-flex shrink-0 items-center gap-2 rounded-[10px] bg-(--ll-blue-subtle) px-3 py-2 text-sm font-semibold text-(--ll-blue) transition hover:bg-(--ll-blue-hover)">
                <x-icon name="calendar-days" class="h-4 w-4" />
                <span class="hidden sm:inline">{{ $isStudent ? 'ปฏิทิน' : 'รอตรวจ' }}</span>
            </a>
        </div>

        {{-- Primary metric + Quick stats --}}
        <div class="border-t border-[#dedee5] p-5 lg:px-7">
            <div class="grid gap-6 sm:grid-cols-2 sm:divide-x sm:divide-[#dedee5]">
                <div class="sm:pr-6">
                    @if($isStudent)
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase text-(--ll-blue)">เลเวลปัจจุบัน</p>
                                <p class="mt-2 text-5xl font-bold leading-none text-[#101114]">{{ $primaryMetric['level'] }}</p>
                            </div>
                            <span class="rounded-[9px] bg-(--ll-blue-subtle) px-2.5 py-1 text-xs font-bold text-(--ll-blue)">
                                {{ number_format($primaryMetric['xp_current']) }} / {{ number_format($primaryMetric['xp_required']) }} XP
                            </span>
                        </div>
                        <div class="dashboard-liquid-progress mt-5 outline-2 outline-[rgba(37,99,235,0.28)]" role="progressbar"
                            aria-label="ความคืบหน้าเลเวล" aria-valuemin="0" aria-valuemax="100"
                            aria-valuenow="{{ $primaryMetric['progress_percent'] }}">
                            <span class="dashboard-liquid-fill" style="width: {{ $primaryMetric['progress_percent'] }}%"></span>
                        </div>
                        <p class="mt-2 text-sm text-[#686b82]">
                            อีก {{ number_format($primaryMetric['remaining']) }} XP จะถึงเลเวล {{ $primaryMetric['level'] + 1 }}
                        </p>
                    @else
                        <p class="text-xs font-bold uppercase text-(--ll-blue)">สถานะการส่งงาน</p>
                        <p class="mt-2 text-5xl font-bold leading-none text-[#101114]">{{ $primaryMetric['rate'] }}%</p>
                        <p class="mt-1 text-sm text-[#686b82]">ตรวจเรียบร้อยแล้ว</p>
                        <div class="mt-5 space-y-2.5">
                            @php
                                $teacherSubTotal = max(1, $primaryMetric['turned_in'] + $primaryMetric['graded'] + $primaryMetric['returned'] + $primaryMetric['assigned']);
                                $teacherStatusBars = [
                                    ['label' => 'ส่งแล้ว', 'count' => $primaryMetric['turned_in'], 'color' => 'bg-blue-500'],
                                    ['label' => 'ตรวจแล้ว', 'count' => $primaryMetric['graded'], 'color' => 'bg-green-500'],
                                    ['label' => 'ส่งคืน', 'count' => $primaryMetric['returned'], 'color' => 'bg-red-500'],
                                    ['label' => 'ยังไม่ส่ง', 'count' => $primaryMetric['assigned'], 'color' => 'bg-gray-300'],
                                ];
                            @endphp
                            @foreach($teacherStatusBars as $bar)
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-[#686b82]">{{ $bar['label'] }}</span>
                                        <span class="font-medium text-[#101114]">{{ $bar['count'] }}</span>
                                    </div>
                                    <div class="h-2 bg-[#f7f5f9] rounded-full overflow-hidden">
                                        <div class="h-full {{ $bar['color'] }} rounded-full transition-all"
                                            style="width: {{ ($bar['count'] / $teacherSubTotal) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="sm:pl-6">
                    <h2 class="text-base font-bold text-[#101114]">สถิติด่วน</h2>
                    <div class="mt-3 space-y-2">
                        @foreach($quickStats as $stat)
                            <div class="flex items-center justify-between gap-3 rounded-[11px] bg-[#f7f5f9] px-3 py-2.5">
                                <span class="flex min-w-0 items-center gap-2.5">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[9px] bg-[rgba(37,99,235,0.14)] text-(--ll-blue)">
                                        <x-icon :name="$stat['icon']" class="h-4 w-4" />
                                    </span>
                                    <span class="truncate text-sm font-semibold text-[#686b82]">{{ $stat['label'] }}</span>
                                </span>
                                <span class="shrink-0 text-base font-bold text-[#101114]">{{ $stat['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity heatmap --}}
        <div data-activity-heatmap class="border-t border-[#dedee5] p-5 lg:px-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-(--ll-blue)">กิจกรรมในรอบ 6 เดือน</p>
                    <h2 class="mt-1 text-lg font-bold text-[#101114]">
                        {{ $isStudent ? 'ความสม่ำเสมอในการเรียนรู้' : 'กิจกรรมการสอน' }}
                    </h2>
                    <p class="mt-1 text-sm text-[#686b82]">
                        {{ $isStudent ? 'การส่งงาน, การเข้าเรียน, ความคิดเห็น และคะแนน' : 'งานในชั้นเรียน, การตรวจงาน และความคิดเห็น' }}
                    </p>
                </div>
                <span class="shrink-0 rounded-[9px] bg-[rgba(37,99,235,0.12)] px-2.5 py-1.5 text-xs font-bold text-(--ll-blue)">
                    {{ number_format($activity['total']) }} กิจกรรม
                </span>
            </div>
            <div class="mt-5 overflow-visible">
                <div class="grid w-max grid-cols-[24px_max-content] gap-2 xl:w-full xl:min-w-0 xl:grid-cols-[24px_minmax(0,1fr)]">
                    <span></span>
                    <div class="activity-grid-cols grid text-xs font-medium text-[#9497a9]">
                        @foreach($monthLabels as $month)
                            <span class="whitespace-nowrap" style="grid-column: {{ $month['week'] }};">{{ $month['label'] }}</span>
                        @endforeach
                    </div>
                    <div class="grid grid-rows-7 gap-1 text-[10px] text-[#9497a9]">
                        <span>จ.</span><span></span><span>พ.</span><span></span>
                        <span>ศ.</span><span></span><span>อา.</span>
                    </div>
                    <div class="activity-grid-cols grid grid-flow-col grid-rows-7 gap-1">
                        @foreach($activityDays as $day)
                            <span data-activity-cell tabindex="0"
                                class="activity-cell tooltip tooltip-top aspect-square size-3 rounded-[3px] outline-none ring-(--ll-blue) transition hover:ring-2 focus:ring-2 {{ ! $day['is_in_year'] || $day['is_future'] ? 'bg-[#f5f3f7]' : $activityLevelClasses[$day['level']] }}"
                                data-tip="{{ $day['label'] }} · {{ $day['count'] }} กิจกรรม"
                                aria-label="{{ $day['label'] }}: {{ $day['count'] }} กิจกรรม"></span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2">
                @foreach($activitySummaries as $summary)
                    <div class="rounded-[11px] bg-[#f7f5f9] p-3">
                        <p class="text-lg font-bold leading-none text-[#101114]">{{ $summary['value'] }}</p>
                        <p class="mt-1 truncate text-xs text-[#686b82]">{{ $summary['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
