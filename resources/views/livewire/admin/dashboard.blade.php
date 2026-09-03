@section('page-title', 'แดชบอร์ดผู้ดูแล')

<div class="space-y-6 bg-gray-50 rounded-2xl border-3 border-gray-200 p-4 sm:p-6">
    {{-- Stats Row 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <x-icon name="users" class="h-4 w-4 text-blue-600" />
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-medium">ผู้ใช้ทั้งหมด</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <span><span class="font-semibold text-green-600">{{ $stats['total_students'] }}</span> นักเรียน</span>
                <span><span class="font-semibold text-blue-600">{{ $stats['total_teachers'] }}</span> ครู</span>
                <span class="text-green-600 font-medium">+{{ $stats['new_users_month'] }} เดือนนี้</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <x-icon name="academic-cap" class="h-4 w-4 text-blue-600" />
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-medium">ห้องเรียน</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_classrooms']) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500"><span class="font-semibold text-blue-600">{{ $stats['active_classrooms'] }}</span> กำลังใช้งาน</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <x-icon name="bolt" class="h-4 w-4 text-blue-600" />
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-medium">XP ทั้งหมดในระบบ</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_xp']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <x-icon name="banknotes" class="h-4 w-4 text-amber-600" />
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-medium">ระบบเศรษฐกิจเหรียญ</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['coins_in_economy']) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500">
                ใช้ไป <span class="font-semibold text-amber-600">{{ number_format($stats['coins_spent']) }}</span>
                เหรียญ
            </p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- User Growth Chart (Line) --}}
        @php
            $gCount = $growthData->count();
            $gMax = max(1, $growthData->max('count'));
            $gMin = $growthData->min('count');
            $gRange = $gMax - $gMin;
            $growthPoints = [];
            foreach ($growthData as $i => $d) {
                $x = $gCount > 1 ? ($i / ($gCount - 1)) * 400 : 200;
                $y = $gRange > 0 ? 65 - (($d['count'] - $gMin) / $gRange) * 45 : 42.5;
                $growthPoints[] = ['x' => $x, 'y' => $y, 'month' => $d['month'], 'count' => $d['count']];
            }
            $gPathD = '';
            foreach ($growthPoints as $i => $p) {
                if ($i === 0) {
                    $gPathD .= "M {$p['x']} {$p['y']}";
                } else {
                    $prev = $growthPoints[$i - 1];
                    $offset = ($p['x'] - $prev['x']) / 3;
                    $gPathD .= " C " . ($prev['x'] + $offset) . " {$prev['y']}, " . ($p['x'] - $offset) . " {$p['y']}, {$p['x']} {$p['y']}";
                }
            }
            $gAreaD = $gPathD . " L 400 80 L 0 80 Z";
        @endphp
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6"
            x-data="{ active: {{ $gCount - 1 }}, points: {{ json_encode($growthPoints) }} }">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h4 class="text-base font-bold text-gray-900">การเติบโตของผู้ใช้</h4>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $period === '12m' ? '12' : '6' }} เดือนล่าสุด</p>
                </div>
                <div class="inline-flex bg-gray-100 rounded-lg p-0.5 text-sm shrink-0">
                    <button wire:click="$set('period', '6m')"
                        class="px-3 py-1.5 rounded-md font-medium transition-colors {{ $period === '6m' ? 'bg-white text-gray-900 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700' }}">
                        6 เดือน
                    </button>
                    <button wire:click="$set('period', '12m')"
                        class="px-3 py-1.5 rounded-md font-medium transition-colors {{ $period === '12m' ? 'bg-white text-gray-900 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700' }}">
                        12 เดือน
                    </button>
                </div>
            </div>
            <div class="relative h-56">
                <svg viewBox="0 0 400 80" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="growthChartGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.2" />
                            <stop offset="100%" stop-color="#3B82F6" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <path d="{{ $gAreaD }}" fill="url(#growthChartGradient)" />
                    <path d="{{ $gPathD }}" fill="none" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" />
                </svg>
                <div class="absolute top-0 bottom-0 w-px border-l border-dashed border-blue-400 pointer-events-none transition-all duration-75"
                    style="opacity: 0.5;"
                    :style="`left: ${points[active].x / 400 * 100}%`">
                </div>
                <div class="absolute pointer-events-none flex items-center justify-center -translate-x-1/2 -translate-y-1/2 transition-all duration-75"
                    :style="`left: ${points[active].x / 400 * 100}%; top: ${points[active].y / 80 * 100}%;`">
                    <span class="absolute w-5 h-5 rounded-full bg-blue-500/30 animate-pulse"></span>
                    <span class="relative w-3 h-3 rounded-full bg-white border-2 border-blue-500 shadow-md"></span>
                </div>
                <div class="absolute inset-0 flex">
                    @foreach($growthPoints as $index => $pt)
                        <div class="h-full flex-1 cursor-pointer"
                            @mouseenter="active = {{ $index }}"
                            @touchstart="active = {{ $index }}">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between bg-gray-50 rounded-xl px-4 py-2 border border-gray-100">
                <span class="font-bold text-gray-600 text-xs" x-text="points[active].month"></span>
                <span class="font-black text-xs text-blue-600" x-text="points[active].count + ' คน'"></span>
            </div>
        </div>

        {{-- Storage Usage (circular) --}}
        @php
            $storagePercent = $storageUsage['percent'];
            $storageRadius = 54;
            $storageCircumference = 2 * M_PI * $storageRadius;
            $storageDashOffset = $storageCircumference * (1 - $storagePercent / 100);
            $storageColor = $storagePercent >= 90 ? '#EF4444' : ($storagePercent >= 70 ? '#F59E0B' : '#3B82F6');
        @endphp
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex flex-col items-center">
            <h4 class="text-base font-bold text-gray-900 mb-1 self-start">พื้นที่จัดเก็บ</h4>
            <p class="text-xs text-gray-500 mb-4 self-start">การใช้งานรวมทั้งระบบ</p>
            <div class="relative w-32 h-32 lg:w-48 lg:h-48">
                <svg viewBox="0 0 128 128" class="w-32 h-32 lg:w-48 lg:h-48 -rotate-90">
                    <circle cx="64" cy="64" r="{{ $storageRadius }}" fill="none" stroke="#e5e7eb" stroke-width="12" />
                    <circle cx="64" cy="64" r="{{ $storageRadius }}" fill="none" stroke="{{ $storageColor }}" stroke-width="12"
                        stroke-linecap="round" stroke-dasharray="{{ $storageCircumference }}"
                        stroke-dashoffset="{{ $storageDashOffset }}" class="transition-all duration-500" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl lg:text-4xl font-black text-gray-900">{{ $storagePercent }}%</span>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-500">
                {{ number_format($storageUsage['used_bytes'] / 1073741824, 2) }} GB / 1 TB
            </p>
        </div>
    </div>

    {{-- Daily Active Students --}}
    <div>
        @php
            $aCount = $dailyActive->count();
            $aMax = max(1, $dailyActive->max('count'));
            $barW = 400 / max(1, $aCount);
            $gap = $barW * 0.25;
            $activePoints = [];
            foreach ($dailyActive as $i => $d) {
                $h = max(($d['count'] / $aMax) * 70, 2);
                $activePoints[] = [
                    'x' => $i * $barW + $gap / 2,
                    'w' => $barW - $gap,
                    'h' => $h,
                    'y' => 80 - $h,
                    'label' => $d['label'],
                    'count' => $d['count'],
                ];
            }
        @endphp
        <div class="bg-white rounded-2xl border border-gray-200 p-6"
            x-data="{ active: {{ $aCount - 1 }}, points: {{ json_encode($activePoints) }} }">
            <h4 class="text-base font-bold text-gray-900 mb-4">นักเรียนที่ active รายวัน (30 วัน)</h4>
            <div class="relative h-40">
                <svg viewBox="0 0 400 80" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                    @foreach($activePoints as $index => $p)
                        <rect x="{{ $p['x'] }}" y="{{ $p['y'] }}" width="{{ $p['w'] }}" height="{{ $p['h'] }}" rx="1.5"
                            :fill="active === {{ $index }} ? '#3B82F6' : '#BFDBFE'"
                            class="transition-colors duration-100" />
                    @endforeach
                </svg>
                <div class="absolute inset-0 flex">
                    @foreach($activePoints as $index => $pt)
                        <div class="h-full flex-1 cursor-pointer"
                            @mouseenter="active = {{ $index }}"
                            @touchstart="active = {{ $index }}">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between bg-gray-50 rounded-xl px-4 py-2 border border-gray-100">
                <span class="font-bold text-gray-600 text-xs" x-text="points[active].label"></span>
                <span class="font-black text-xs text-blue-600" x-text="points[active].count + ' คน'"></span>
            </div>
        </div>
    </div>
</div>
