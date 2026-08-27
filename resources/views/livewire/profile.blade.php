<div class="max-w-4xl mx-auto">
    @section('page-title', 'โปรไฟล์')

    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

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
                    <h1 class="truncate text-3xl font-black sm:text-4xl {{ $user->active_name_color ?: 'text-slate-950' }}">
                        {{ $user->name }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                        @foreach($user->displayed_badges as $badge)
                            <img src="{{ asset($badge->badge_image ?: 'images/achievements/Achievements_Novice.png') }}"
                                alt="{{ $badge->name }}" title="{{ $badge->name }}" class="h-7 w-7 shrink-0 object-contain">
                        @endforeach
                        <img src="{{ asset(match($user->role) { 'student' => 'images/badge_student.png', 'teacher' => 'images/badge_teacher.png', 'admin' => 'images/badge_administrator.png', default => 'images/badge_student.png' }) }}"
                            alt="{{ match($user->role) { 'student' => 'นักเรียน', 'teacher' => 'ครู', 'admin' => 'แอดมิน', default => ucfirst($user->role) } }}"
                            class="h-7 w-auto shrink-0 object-contain">
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats + Side Content --}}
        <div class="p-5 lg:px-7 space-y-6">

            {{-- Bio --}}
            <div class="rounded-xl border border-[#dedee5] bg-white p-4 sm:p-5">
                <p class="text-sm leading-6 text-slate-600">
                    {{ $user->bio ?: 'โปรไฟล์การเรียนรู้, ความคืบหน้าในห้องเรียน และการสะสมความสำเร็จ' }}
                </p>
            </div>

            {{-- Level progress bar (student only) --}}
            @if($user->isStudent())
            <div class="rounded-xl border border-[#dedee5] bg-white p-4 sm:p-5 flex items-center gap-4 sm:gap-5">
                {{-- Left: Level Number with light blue circular background --}}
                <div class="flex flex-col items-center justify-center rounded-full bg-(--ll-blue-subtle) h-16 w-16 sm:h-18 sm:w-18 shrink-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-(--ll-blue)">เลเวล</span>
                    <span class="text-2xl sm:text-3xl font-black leading-none text-[#101114] mt-0.5">{{ $profileStats['level'] }}</span>
                </div>

                {{-- Right: Dashboard-style level progressbar --}}
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold uppercase text-(--ll-blue)">ความคืบหน้าเลเวล</span>
                        <span class="rounded-[9px] bg-(--ll-blue-subtle) px-2.5 py-0.5 text-xs font-bold text-(--ll-blue)">
                            {{ number_format($profileStats['xp_current']) }} / {{ number_format($profileStats['xp_required']) }} XP
                        </span>
                    </div>
                    <div class="dashboard-liquid-progress outline-2 outline-[rgba(37,99,235,0.28)] mt-2" role="progressbar"
                        aria-label="ความคืบหน้าเลเวล" aria-valuemin="0" aria-valuemax="100"
                        aria-valuenow="{{ $profileStats['level_progress_percent'] }}">
                        <span class="dashboard-liquid-fill" style="width: {{ $profileStats['level_progress_percent'] }}%"></span>
                    </div>
                    <p class="mt-1.5 text-xs text-[#686b82]">
                        อีก {{ number_format($profileStats['xp_remaining']) }} XP จะถึงเลเวล {{ $profileStats['level'] + 1 }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Stat boxes --}}
            <div class="grid grid-cols-2 gap-3 {{ $user->isStudent() ? 'sm:grid-cols-4' : 'sm:grid-cols-3' }}">
                <div class="rounded-xl border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">{{ number_format($profileStats['classrooms']) }}</p>
                    <p class="text-xs font-semibold text-[#9497a9]">ห้องเรียน</p>
                </div>
                <div class="rounded-xl border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">
                        @if($user->isStudent())
                            {{ number_format($profileStats['submissions']) }}
                        @else
                            {{ number_format($profileStats['assignments_created']) }}
                        @endif
                    </p>
                    <p class="text-xs font-semibold text-[#9497a9]">{{ $user->isStudent() ? 'งานที่ส่ง' : 'งานที่มอบหมาย' }}</p>
                </div>
                <div class="rounded-xl border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">
                        @if($user->isStudent())
                            {{ number_format($profileStats['average_score'], 1) }}
                        @else
                            {{ number_format($profileStats['graded_submissions']) }}
                        @endif
                    </p>
                    <p class="text-xs font-semibold text-[#9497a9]">{{ $user->isStudent() ? 'คะแนนเฉลี่ย' : 'งานที่ตรวจแล้ว' }}</p>
                </div>
                @if($user->isStudent())
                <div class="rounded-xl border border-[#dedee5] bg-white p-4">
                    <p class="text-2xl font-black text-[#101114]">{{ $profileStats['achievements'] }}/{{ $profileStats['achievement_total'] }}</p>
                    <p class="text-xs font-semibold text-[#9497a9]">ปลดล็อคแล้ว</p>
                </div>
                @endif
            </div>

            {{-- Rank chart (student only) --}}
            @if($user->isStudent() && !empty($chartPoints))
                <div x-data="rankChart({{ json_encode($chartPoints) }})" wire:ignore>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-[#dedee5] pb-4">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">อันดับและแนวโน้มการเรียน</h2>
                            <p class="text-xs text-slate-500">แสดงประวัติอันดับความคืบหน้าในช่วง 90 วันที่ผ่านมา</p>
                        </div>
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">อันดับ</p>
                                <p class="text-3xl font-black text-slate-900" x-text="'#' + activeRank"></p>
                            </div>
                        </div>
                    </div>
                    <div class="relative mt-5 h-24" x-ref="chart"></div>
                </div>
            @endif

            {{-- Achievements (student only) --}}
            @if($user->isStudent())
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
                        <article class="group rounded-xl border p-4 text-center transition {{ $unlocked ? 'border-[rgba(37,99,235,0.4)] bg-(--ll-blue-hover)' : 'border-[#dedee5] bg-[rgba(104,107,130,0.04)] opacity-70 grayscale' }}">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full {{ $unlocked ? 'bg-white' : 'bg-slate-100' }}">
                                @if($achievement->badge_image)
                                    <img src="{{ asset($achievement->badge_image).'?v='.@filemtime(public_path($achievement->badge_image)) }}" alt="{{ $achievement->name }}"
                                        class="h-12 w-12 object-contain">
                                @else
                                    <x-icon name="medal" class="h-6 w-6 {{ $unlocked ? 'text-[#2563eb]' : 'text-slate-400' }}" />
                                @endif
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-sm font-black text-[#101114]">{{ $achievement->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-[#686b82]">{{ $achievement->description }}</p>
                        </article>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-[#dedee5] bg-[rgba(104,107,130,0.04)] px-5 py-10 text-center text-sm text-[#9497a9]">
                            ยังไม่ได้ตั้งค่าความสำเร็จ
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-[#dedee5]"></div>
            @endif

            {{-- Classrooms --}}
            <div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black text-[#101114]">ห้องเรียน</h2>
                        <p class="mt-1 text-sm text-[#686b82]">ห้องเรียนที่เชื่อมต่อกับโปรไฟล์นี้</p>
                    </div>
                    @if($isOwnProfile)
                        <a href="{{ route('classrooms') }}" wire:navigate class="text-sm font-bold text-[#2563eb] hover:text-[#1d4ed8]">
                            ดูทั้งหมด
                            <x-icon name="chevron-right" class="h-4 w-4 ml-1" />
                        </a>
                    @endif
                </div>
                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    @forelse($profileClassrooms as $index => $classroom)
                        @php
                            $tc = $classroom->themeCategory;
                            $planetKey = $tc?->planet_key ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['planet_key'];
                        @endphp
                        @if($isOwnProfile)
                            <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
                                class="group flex min-w-0 items-center gap-4 rounded-xl border border-[#dedee5] bg-white p-4 transition hover:-translate-y-0.5 hover:border-[rgba(37,99,235,0.3)]">
                        @else
                            <div class="flex min-w-0 items-center gap-4 rounded-xl border border-[#dedee5] bg-white p-4">
                        @endif
                                <img src="/images/planets/planet_{{ $planetKey }}.svg" alt="{{ $classroom->name }}"
                                    class="h-16 w-16 shrink-0 select-none" />
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate font-black text-[#101114] group-hover:text-[#2563eb]">
                                        {{ $classroom->name }}
                                        @if($classroom->section)
                                            <span class="text-[#9497a9] font-medium">({{ $classroom->section }})</span>
                                        @endif
                                    </h3>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold text-[#686b82]">
                                        <span class="min-w-0 flex-1 truncate rounded-full bg-slate-100 px-2.5 py-1">
                                            <x-icon name="user" class="h-4 w-4 mr-1 text-slate-400" />{{ $classroom->teacher?->name }}
                                        </span>
                                        <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1">
                                            <x-icon name="document" class="h-4 w-4 mr-1 text-slate-400" />{{ $classroom->assignments_count }}
                                        </span>
                                    </div>
                                </div>
                        @if($isOwnProfile)
                            </a>
                        @else
                            </div>
                        @endif
                    @empty
                        <div class="rounded-xl border border-dashed border-[#dedee5] bg-[rgba(104,107,130,0.04)] px-5 py-10 text-center text-sm text-[#9497a9] md:col-span-2">
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
                            @if($isOwnProfile)
                                <a href="{{ $assignment && $classroom ? route('assignment.show', [$classroom, $assignment]) : '#' }}" wire:navigate
                                    class="block min-w-0 rounded-xl border border-[#dedee5] p-3 transition hover:border-[rgba(37,99,235,0.3)] hover:bg-[rgba(59,130,246,0.04)]">
                            @else
                                <div class="min-w-0 rounded-xl border border-[#dedee5] p-3">
                            @endif
                                <div class="flex items-start gap-3">
                                    <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-white"
                                        style="background-color: {{ $classroom?->themeCategory?->color ?? ($classroom ? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'] : '#2563eb') }};">
                                        <x-icon name="document" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-bold text-[#101114]">{{ $assignment?->title ?? 'งาน' }}</span>
                                        <span class="mt-1 block truncate text-xs text-[#9497a9]">{{ $submission->turned_in_at?->diffForHumans() }}</span>
                                    </span>
                                </div>
                            @if($isOwnProfile)
                                </a>
                            @else
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
