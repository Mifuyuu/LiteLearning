@section('page-title', 'แดชบอร์ดผู้ดูแล')

<div class="space-y-6">
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
                    <x-icon name="document-text" class="h-4 w-4 text-blue-600" />
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-medium">งาน / การส่ง</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_assignments']) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500">
                <span class="font-semibold text-blue-600">{{ number_format($stats['total_submissions']) }}</span> การส่ง
                <span class="text-amber-600 font-medium ml-2">{{ $stats['pending_grading'] }} รอตรวจ</span>
            </p>
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
        {{-- User Growth Chart (Bars) --}}
        @php $barMax = max(1, $growthData->max('count')); @endphp
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
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
            <div class="h-64 flex items-end justify-between gap-3 px-2">
                @foreach($growthData as $data)
                    <div class="flex flex-col items-center flex-1 gap-2">
                        <div class="w-full max-w-10 bg-blue-500 rounded-t-lg transition-all hover:bg-blue-600 relative group"
                            style="height: {{ max(4, ($data['count'] / $barMax) * 200) }}px">
                            <div class="absolute -top-9 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                {{ $data['count'] }} คน
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 font-medium">{{ $data['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Completion Rate --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h4 class="text-base font-bold text-gray-900 mb-1">สถานะการส่งงาน</h4>
            <p class="text-xs text-gray-500 mb-4">ภาพรวมการส่งงานทั้งหมด</p>

            <div class="text-center mb-6">
                <span class="text-4xl font-black text-blue-600">{{ $completionData['rate'] }}%</span>
                <p class="text-xs text-gray-500 mt-1">ตรวจเรียบร้อย</p>
            </div>

            <div class="space-y-3">
                @php
                    $total = max(1, $completionData['turned_in'] + $completionData['graded'] + $completionData['returned'] + $completionData['assigned']);
                    $bars = [
                        ['label' => 'ส่งแล้ว', 'count' => $completionData['turned_in'], 'color' => 'bg-blue-500'],
                        ['label' => 'ตรวจแล้ว', 'count' => $completionData['graded'], 'color' => 'bg-green-500'],
                        ['label' => 'ส่งคืน', 'count' => $completionData['returned'], 'color' => 'bg-blue-500'],
                        ['label' => 'ยังไม่ส่ง', 'count' => $completionData['assigned'], 'color' => 'bg-gray-300'],
                    ];
                @endphp
                @foreach($bars as $bar)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">{{ $bar['label'] }}</span>
                            <span class="font-medium text-gray-900">{{ $bar['count'] }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $bar['color'] }} rounded-full transition-all"
                                style="width: {{ ($bar['count'] / $total) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tables Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Classroom Activity --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h4 class="text-base font-bold text-gray-900 mb-4">ห้องเรียนที่เคลื่อนไหวมากที่สุด</h4>
            <div class="divide-y divide-gray-100">
                @forelse($classroomActivity as $item)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-2.5 h-2.5 rounded-full shrink-0"
                                style="background-color: {{ $item['classroom']->themeCategory?->color ?? '#8B5CF6' }}">
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item['classroom']->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item['student_count'] }} คน</p>
                            </div>
                        </div>
                        <div class="text-right text-xs text-gray-500 shrink-0 ml-4">
                            <p><span class="font-semibold text-gray-900">{{ $item['submissions'] }}</span> ส่ง</p>
                            <p><span class="font-semibold text-green-600">{{ $item['graded'] }}</span> ตรวจ
                                @if($item['avg_score']) | เฉลี่ย {{ $item['avg_score'] }} @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">ไม่มีข้อมูล</p>
                @endforelse
            </div>
        </div>

        {{-- Top Active Users --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h4 class="text-base font-bold text-gray-900 mb-4">นักเรียนที่ส่งงานมากที่สุด</h4>
            <div class="divide-y divide-gray-100">
                @forelse($topUsers as $item)
                    <a href="{{ route('profile', $item['user']) }}" wire:navigate class="flex items-center gap-3 py-2.5 transition hover:bg-gray-50 rounded-lg px-2 -mx-2">
                        <span class="text-xs font-bold text-gray-400 w-5 shrink-0 text-center">{{ $loop->iteration }}</span>
                        <img src="{{ $item['user']->avatar_url }}" class="w-8 h-8 rounded-full shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $item['user']->name }}</p>
                            <p class="text-xs text-gray-500">ส่ง {{ $item['submissions'] }} งาน</p>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 shrink-0">{{ number_format($item['xp']) }} XP</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">ไม่มีข้อมูล</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Daily Active + Store + Bug Reports --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Daily Active Students --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <h4 class="text-base font-bold text-gray-900 mb-4">นักเรียนที่ active รายวัน (30 วัน)</h4>
            <div class="h-40 flex items-end justify-between gap-0.5">
                @php $maxActive = max(1, $dailyActive->max('count')); @endphp
                @foreach($dailyActive as $data)
                    <div class="flex-1 flex flex-col items-center justify-end gap-0.5 group relative">
                        <div class="w-full bg-blue-400 hover:bg-blue-500 rounded-sm transition-all"
                            style="height: {{ max(2, ($data['count'] / $maxActive) * 120) }}px">
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                {{ $data['count'] }} คน
                            </div>
                        </div>
                        <span class="text-[8px] text-gray-400 mt-0.5 hidden sm:block">{{ $data['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Store + Bug Reports --}}
        <div class="space-y-6">
            {{-- Store Economy --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h4 class="text-base font-bold text-gray-900 mb-3">ร้านค้ายอดนิยม</h4>
                <div class="space-y-2">
                    @forelse($storeEconomy['popular_items'] as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700 truncate mr-2">{{ $item['name'] }}</span>
                            <span class="font-medium text-gray-900 shrink-0">{{ $item['count'] }} ชิ้น</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">ยังไม่มีข้อมูล</p>
                    @endforelse
                    <div class="border-t border-gray-100 pt-2 mt-2 text-xs text-gray-500">
                        ธุรกรรมทั้งหมด {{ number_format($storeEconomy['total_transactions']) }} ครั้ง
                    </div>
                </div>
            </div>

            {{-- Bug Reports (keep existing) --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-gray-900">รายงานปัญหาล่าสุด</h4>
                    <a href="{{ route('admin.reports') }}" class="text-sm text-blue-600 font-medium hover:text-blue-700">ดูทั้งหมด</a>
                </div>
                <div class="space-y-3">
                    @forelse($stats['recent_bug_reports'] as $report)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shrink-0">
                                <x-icon name="bug" class="h-4 w-4 text-red-500" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-700 truncate">{{ $report->message }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $report->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">ไม่มีรายงานปัญหาล่าสุด</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
