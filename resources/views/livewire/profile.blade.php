<div class="max-w-4xl mx-auto">
    @section('page-title', 'โปรไฟล์')

    @php
        $completion = $profileStats['achievement_total'] > 0
            ? (int) round(($profileStats['achievements'] / $profileStats['achievement_total']) * 100)
            : 0;
    @endphp

    <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- Cover + Bio --}}
        <div class="relative h-48 overflow-hidden bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.35),transparent_24%),linear-gradient(135deg,#1e40af,#2563eb_48%,#3b82f6)]">
            <img src="{{ $user->cover_image ? $user->cover_image_url : asset('images/default_profile_banner.webp').'?v='.filemtime(public_path('images/default_profile_banner.webp')) }}" alt="{{ $user->name }}"
                class="absolute inset-0 h-full w-full object-cover">
            @if($user->cover_image)
                <div class="absolute inset-0 bg-slate-950/35"></div>
            @endif
        </div>

        <div class="px-5 pb-6 lg:px-7">
            <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-start sm:gap-20">
                <div class="-mt-10 sm:-mt-12 relative inline-block shrink-0 self-center group sm:ml-16">
                    <x-user-avatar :user="$user" size="w-36 h-36" border="border-4 border-white" shadow="shadow-none" />
                </div>
                <div class="min-w-0 pb-1 text-center sm:pt-4 sm:text-left">
                    <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                        <h1 class="truncate text-3xl font-black sm:text-4xl {{ $user->active_name_color ?: 'text-slate-950' }}">
                            {{ $user->name }}
                        </h1>
                        <span class="inline-flex items-center gap-1 rounded-full bg-[rgba(59,130,246,0.16)] px-2.5 py-1 text-xs font-black uppercase tracking-wide text-[#2563eb]">
                            <x-icon name="user-solid" class="h-3.5 w-3.5 shrink-0" />
                            {{ match($user->role) { 'student' => 'นักเรียน', 'teacher' => 'ครู', 'admin' => 'แอดมิน', default => ucfirst($user->role) } }}
                        </span>
                    </div>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                        {{ $user->bio ?: 'โปรไฟล์การเรียนรู้, ความคืบหน้าในห้องเรียน และการสะสมความสำเร็จ' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-[#dedee5]"></div>

        {{-- Stats + Side Content --}}
        <div class="p-5 lg:px-7 space-y-6">

            {{-- Achievements progress bar --}}
            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold text-[#686b82]">คอลเลกชันความสำเร็จ</span>
                    <span class="font-black text-[#2563eb]">{{ $completion }}%</span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-[rgba(59,130,246,0.08)]">
                    <div class="h-full rounded-full bg-[#2563eb]" style="width: {{ $completion }}%"></div>
                </div>
            </div>

            {{-- Stat boxes --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">{{ number_format($profileStats['classrooms']) }}</p>
                    <p class="text-xs font-semibold text-[#9497a9]">ห้องเรียน</p>
                </div>
                <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">{{ number_format($profileStats['submissions']) }}</p>
                    <p class="text-xs font-semibold text-[#9497a9]">งานที่ส่ง</p>
                </div>
                <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">{{ number_format($profileStats['average_score'], 1) }}</p>
                    <p class="text-xs font-semibold text-[#9497a9]">คะแนนเฉลี่ย</p>
                </div>
                <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">{{ $profileStats['achievements'] }}/{{ $profileStats['achievement_total'] }}</p>
                    <p class="text-xs font-semibold text-[#9497a9]">ปลดล็อคแล้ว</p>
                </div>
            </div>

            {{-- Rank chart (student only) --}}
            @if($user->isStudent() && !empty($chartPoints))
                @php
                    $pathD = '';
                    $areaD = '';
                    foreach ($chartPoints as $i => $p) {
                        if ($i === 0) {
                            $pathD .= "M {$p['x']} {$p['y']}";
                        } else {
                            $prev = $chartPoints[$i - 1];
                            $offset = ($p['x'] - $prev['x']) / 3;
                            $cp1x = $prev['x'] + $offset;
                            $cp1y = $prev['y'];
                            $cp2x = $p['x'] - $offset;
                            $cp2y = $p['y'];
                            $pathD .= " C {$cp1x} {$cp1y}, {$cp2x} {$cp2y}, {$p['x']} {$p['y']}";
                        }
                    }
                    $areaD = $pathD . " L 400 80 L 0 80 Z";
                @endphp
                <div x-data="{
                        activePoint: {{ count($chartPoints) - 1 }},
                        points: {{ json_encode($chartPoints) }}
                    }">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-[#dedee5] pb-4">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">อันดับและแนวโน้มการเรียน</h2>
                            <p class="text-xs text-slate-500">แสดงประวัติอันดับความคืบหน้าในช่วง 90 วันที่ผ่านมา</p>
                        </div>
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">อันดับ</p>
                                <p class="text-3xl font-black text-slate-900" x-text="'#' + Number(points[activePoint].rank).toLocaleString()"></p>
                            </div>
                        </div>
                    </div>
                    <div class="relative mt-5 h-24">
                        <svg viewBox="0 0 400 80" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#2563eb" stop-opacity="0.15" />
                                    <stop offset="100%" stop-color="#2563eb" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <path d="{{ $areaD }}" fill="url(#chartGradient)" />
                            <path d="{{ $pathD }}" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" />
                        </svg>
                        <div class="absolute top-0 bottom-0 w-px border-l border-dashed pointer-events-none transition-all duration-75"
                            :style="`left: ${points[activePoint].x / 400 * 100}%; border-color: #2563eb;`"
                            style="opacity: 0.5;">
                        </div>
                        <div class="absolute pointer-events-none flex items-center justify-center -translate-x-1/2 -translate-y-1/2 transition-all duration-75"
                            :style="`left: ${points[activePoint].x / 400 * 100}%; top: ${points[activePoint].y / 80 * 100}%;`">
                            <span class="absolute w-5 h-5 rounded-full animate-pulse" style="background-color: rgba(37,99,235,0.3);"></span>
                            <span class="relative w-3 h-3 rounded-full bg-white shadow-md" style="border: 2.5px solid #2563eb;"></span>
                        </div>
                        <div class="absolute inset-0 flex">
                            @foreach($chartPoints as $index => $pt)
                                <div class="h-full flex-1 cursor-pointer"
                                    @mouseenter="activePoint = {{ $index }}"
                                    @touchstart="activePoint = {{ $index }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between bg-slate-50 rounded-[12px] px-4 py-2 border border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #2563eb;"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2" style="background-color: #2563eb;"></span>
                            </span>
                            <span class="font-bold text-slate-600 text-xs" x-text="points[activePoint].day"></span>
                        </div>
                        <span class="font-black text-xs" style="color: #2563eb;" x-text="'อันดับ #' + Number(points[activePoint].rank).toLocaleString()"></span>
                    </div>
                </div>
            @endif

            <div class="border-t border-[#dedee5]"></div>

            {{-- Achievements --}}
            <div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black text-[#101114]">คอลเลกชันความสำเร็จ</h2>
                        <p class="mt-1 text-sm text-[#686b82]">ความสำเร็จที่ปลดล็อคแล้วจะถูกเน้น</p>
                    </div>
                    <span class="rounded-full bg-[rgba(59,130,246,0.16)] px-3 py-1 text-sm font-black text-[#2563eb]">
                        {{ $profileStats['achievements'] }} / {{ $profileStats['achievement_total'] }}
                    </span>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                    @forelse($achievements as $achievement)
                        @php
                            $unlocked = isset($unlockedAchievementIds[$achievement->id]);
                        @endphp
                        <article class="group rounded-[12px] border p-4 text-center transition {{ $unlocked ? 'border-[rgba(37,99,235,0.4)] bg-[var(--ll-blue-hover)]' : 'border-[#dedee5] bg-[rgba(104,107,130,0.04)] opacity-70 grayscale' }}">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full {{ $unlocked ? 'bg-white' : 'bg-slate-100' }}">
                                @if($achievement->badge_image)
                                    <img src="{{ asset($achievement->badge_image) }}" alt="{{ $achievement->name }}"
                                        class="h-12 w-12 object-contain">
                                @else
                                    <x-icon name="medal" class="h-6 w-6 {{ $unlocked ? 'text-[#2563eb]' : 'text-slate-400' }}" />
                                @endif
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-sm font-black text-[#101114]">{{ $achievement->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-[#686b82]">{{ $achievement->description }}</p>
                        </article>
                    @empty
                        <div class="col-span-full rounded-[12px] border border-dashed border-[#dedee5] bg-[rgba(104,107,130,0.04)] px-5 py-10 text-center text-sm text-[#9497a9]">
                            ยังไม่ได้ตั้งค่าความสำเร็จ
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-[#dedee5]"></div>

            {{-- Classrooms --}}
            <div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black text-[#101114]">ห้องเรียน</h2>
                        <p class="mt-1 text-sm text-[#686b82]">ห้องเรียนที่เชื่อมต่อกับโปรไฟล์นี้</p>
                    </div>
                    <a href="{{ route('classrooms') }}" wire:navigate class="text-sm font-bold text-[#2563eb] hover:text-[#1d4ed8]">
                        ดูทั้งหมด
                        <x-icon name="chevron-right" class="h-4 w-4 ml-1" />
                    </a>
                </div>
                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    @forelse($profileClassrooms as $entry)
                        @php
                            $classroom = $entry['model'];
                        @endphp
                        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
                            class="group overflow-hidden rounded-[12px] border border-[#dedee5] bg-white transition hover:-translate-y-0.5 hover:border-[rgba(37,99,235,0.3)]">
                            <div class="h-20"
                                style="background-color: {{ $classroom->themeCategory?->color ?? '#2563eb' }};"></div>
                            <div class="p-4">
                                <div class="-mt-10 mb-3 flex h-12 w-12 items-center justify-center rounded-[12px] border-4 border-white bg-white text-[#2563eb]">
                                    <x-icon name="book-open" class="h-5 w-5" />
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-black text-[#101114] group-hover:text-[#2563eb]">{{ $classroom->name }}</h3>
                                        <p class="mt-1 truncate text-sm text-[#686b82]">{{ $classroom->section ?: 'ไม่มีหมวด' }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-[rgba(59,130,246,0.16)] px-2.5 py-1 text-xs font-black text-[#2563eb]">
                                        {{ $entry['role'] }}
                                    </span>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-[#686b82]">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                        <x-icon name="users" class="h-4 w-4 mr-1 text-slate-400" />{{ $entry['students_count'] }}
                                    </span>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                        <x-icon name="document-text" class="h-4 w-4 mr-1 text-slate-400" />{{ $entry['assignments_count'] }}
                                    </span>
                                    <span class="min-w-0 truncate rounded-full bg-slate-100 px-2.5 py-1">
                                        <x-icon name="user" class="h-4 w-4 mr-1 text-slate-400" />{{ $classroom->teacher?->name }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-[12px] border border-dashed border-[#dedee5] bg-[rgba(104,107,130,0.04)] px-5 py-10 text-center text-sm text-[#9497a9] md:col-span-2">
                            ยังไม่มีห้องเรียน
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent submissions (only if has any) --}}
            @if($recentSubmissions->isNotEmpty())
                <div class="border-t border-[#dedee5]"></div>
                <div>
                    <h2 class="text-lg font-black text-[#101114] mb-4">ผลงานล่าสุด</h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($recentSubmissions as $submission)
                            @php
                                $assignment = $submission->assignment;
                                $classroom = $assignment?->classroom;
                            @endphp
                            <a href="{{ $assignment && $classroom ? route('assignment.show', [$classroom, $assignment]) : '#' }}" wire:navigate
                                class="block rounded-[12px] border border-[#dedee5] p-3 transition hover:border-[rgba(37,99,235,0.3)] hover:bg-[rgba(59,130,246,0.04)]">
                                <div class="flex items-start gap-3">
                                    <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-[12px] text-white"
                                        style="background-color: {{ $classroom?->themeCategory?->color ?? '#2563eb' }};">
                                        <x-icon name="document-text" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-bold text-[#101114]">{{ $assignment?->title ?? 'งาน' }}</span>
                                        <span class="mt-1 block truncate text-xs text-[#9497a9]">{{ $submission->turned_in_at?->diffForHumans() }}</span>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
