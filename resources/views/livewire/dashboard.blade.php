@section('page-title', __('Dashboard'))

@php
    $user = auth()->user();
    $isStudent = $user->isStudent();
    $isTeacher = $user->isTeacher() || $user->isAdmin();
    $displayName = trim(explode(' ', $user->name)[0] ?? $user->name);
    $firstClassroom = $classrooms->first();
    $primaryClassroomRoute = $firstClassroom ? route('classroom.show', $firstClassroom) : route('classrooms');
    $createRoute = $firstClassroom ? route('assignment.create', $firstClassroom) : route('classrooms');

    $heroMetric = $isStudent
        ? [
            'label' => __('Level'),
            'value' => $gamification['level'] ?? 1,
            'caption' => __(':xp XP to next level', ['xp' => number_format($gamification['xp_to_next'] ?? 0)]),
            'progress' => $gamification['progress_percent'] ?? 0,
            'icon' => 'fire',
        ]
        : [
            'label' => __('Pending Review'),
            'value' => $stats['pending'] ?? 0,
            'caption' => __('submissions waiting for feedback'),
            'progress' => min(100, (($stats['pending'] ?? 0) * 12)),
            'icon' => 'clipboard-document-list',
        ];

    $statsGrid = $isStudent
        ? [
            ['label' => __('Coins'), 'value' => number_format($gamification['coins'] ?? 0), 'icon' => 'star-solid', 'tone' => 'text-amber-700 bg-amber-50'],
            ['label' => __('Achievements'), 'value' => number_format($gamification['achievements'] ?? 0), 'icon' => 'star', 'tone' => 'text-amber-700 bg-amber-50'],
            ['label' => __('Assignments'), 'value' => number_format($studentAnalytics['totals']['assignments'] ?? 0), 'icon' => 'document-text', 'tone' => 'text-indigo-700 bg-indigo-50'],
            ['label' => __('Average Score'), 'value' => number_format($studentAnalytics['totals']['average_score'] ?? 0, 1), 'icon' => 'chart-bar', 'tone' => 'text-violet-700 bg-violet-50'],
        ]
        : [
            ['label' => __('Classrooms'), 'value' => number_format($stats['classrooms'] ?? 0), 'icon' => 'academic-cap', 'tone' => 'text-indigo-700 bg-indigo-50'],
            ['label' => __('Students'), 'value' => number_format($stats['students'] ?? 0), 'icon' => 'users', 'tone' => 'text-sky-700 bg-sky-50'],
            ['label' => __('Assignments'), 'value' => number_format($stats['assignments'] ?? 0), 'icon' => 'document-text', 'tone' => 'text-violet-700 bg-violet-50'],
            ['label' => __('Pending'), 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'clock', 'tone' => 'text-amber-700 bg-amber-50'],
        ];

    $activityItems = $upcomingAssignments->take(3)->map(fn ($assignment) => [
        'title' => $assignment->title,
        'meta' => $assignment->classroom?->name ?? __('Classroom'),
        'time' => $assignment->due_date?->diffForHumans() ?? __('No due date'),
        'route' => route('assignment.show', [$assignment->classroom, $assignment]),
        'icon' => $assignment->isProject() ? 'clipboard-document-list' : 'document-text',
        'color' => $assignment->classroom?->themeCategory?->color ?? '#7132f5',
    ]);

    $quickLinks = $isStudent
        ? [
            ['label' => __('Achievements'), 'route' => route('achievements'), 'icon' => 'star'],
            ['label' => __('Leaderboard'), 'route' => route('leaderboard'), 'icon' => 'trophy'],
            ['label' => __('Store'), 'route' => route('store'), 'icon' => 'shopping-bag'],
        ]
        : [
            ['label' => __('Create Classwork'), 'route' => $createRoute, 'icon' => 'plus'],
            ['label' => __('To Review'), 'route' => route('to-review'), 'icon' => 'clipboard-document-list'],
            ['label' => __('Classrooms'), 'route' => route('classrooms'), 'icon' => 'academic-cap'],
        ];

    $weekDays = collect(['M', 'T', 'W', 'T', 'F', 'S', 'S']);
    $recentSubmissions = collect($studentAnalytics['charts']['recent_activity']['submissions'] ?? []);
@endphp

<div class="animate__animated animate__fadeIn">
    <div class="space-y-6">
        {{-- Hero Section --}}
        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px] xl:items-stretch">
            <div class="overflow-hidden rounded-2xl border border-[#dedee5] bg-gradient-to-br from-white to-[rgba(243,240,255,0.6)] p-5 sm:p-6 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="inline-flex items-center rounded-full bg-[rgba(133,91,251,0.16)] px-3 py-1 text-xs font-semibold text-[#7132f5]">
                            <x-icon name="sparkles" class="mr-1.5 h-3.5 w-3.5" />
                            {{ $isStudent ? __('Learning workspace') : __('Teaching workspace') }}
                        </div>
                        <h1 class="mt-4 max-w-3xl text-3xl font-bold leading-tight text-[#101114] sm:text-5xl" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -1px;">
                            {{ __('Great to see you, :name!', ['name' => $displayName]) }}
                        </h1>
                        <p class="mt-3 max-w-2xl text-base leading-7 text-[#686b82]">
                            {{ $isStudent ? __('Keep your learning day moving.') : __('Keep classes, reviews, and classwork in one place.') }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('classrooms') }}" wire:navigate
                                class="inline-flex items-center gap-2 rounded-[12px] border border-[#5741d8] bg-white px-4 py-[13px] text-sm font-semibold text-[#5741d8] transition hover:bg-[rgba(133,91,251,0.08)]">
                                <x-icon name="academic-cap" class="h-4 w-4" />
                                {{ __('Classrooms') }}
                            </a>
                            <a href="{{ $isTeacher ? $createRoute : route('calendar') }}" wire:navigate
                                class="inline-flex items-center gap-2 rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white transition hover:bg-[#5741d8]">
                                <x-icon :name="$isTeacher ? 'plus' : 'calendar-days'" class="h-4 w-4" />
                                {{ $isTeacher ? __('New') : __('Calendar') }}
                            </a>
                        </div>
                    </div>

                    <div class="hidden shrink-0 lg:block">
                        <div class="ll-mascot-badge">
                            <div class="ll-mascot-face">
                                <span></span>
                                <span></span>
                            </div>
                            <div class="ll-mascot-wing"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hero Metric Card --}}
            <aside class="rounded-2xl border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-[#686b82]">{{ $heroMetric['label'] }}</p>
                        <p class="mt-2 text-5xl font-bold leading-none text-[#7132f5]" style="font-family: 'IBM Plex Sans', sans-serif;">{{ number_format($heroMetric['value']) }}</p>
                    </div>
                    <span class="flex h-14 w-14 items-center justify-center rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5]">
                        <x-icon :name="$heroMetric['icon']" class="h-7 w-7" />
                    </span>
                </div>
                <p class="mt-4 text-sm font-semibold text-[#686b82]">{{ $heroMetric['caption'] }}</p>
                <div class="mt-5 h-3 overflow-hidden rounded-full bg-[#f5f3ff]">
                    <div class="h-full rounded-full bg-[#7132f5]" style="width: {{ $heroMetric['progress'] }}%"></div>
                </div>
                <div class="mt-6 grid grid-cols-7 gap-1.5 sm:gap-2">
                    @foreach($weekDays as $index => $day)
                        @php
                            $isComplete = $isStudent
                                ? (($recentSubmissions[$index] ?? 0) > 0)
                                : ($index < min(7, max(1, $upcomingAssignments->count())));
                        @endphp
                        <div class="text-center">
                            <p class="text-xs font-semibold text-[#9497a9]">{{ $day }}</p>
                            <span class="mx-auto mt-1.5 flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full {{ $isComplete ? 'bg-[#7132f5] text-white' : 'border border-[#dedee5] bg-white text-[#9497a9]' }}">
                                <x-icon :name="$isComplete ? 'check' : 'circle'" class="h-3.5 w-3.5" />
                            </span>
                        </div>
                    @endforeach
                </div>
            </aside>
        </section>

        {{-- Stats Grid --}}
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($statsGrid as $item)
                <article class="group rounded-2xl border border-[#dedee5] bg-white p-4 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition-all duration-300 hover:-translate-y-1 hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px] hover:border-[rgba(113,50,245,0.3)]">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-[12px] {{ $item['tone'] }}">
                            @if(str_starts_with($item['icon'], 'gsi-'))
                                <i class="{{ $item['icon'] }} text-xl"></i>
                            @else
                                <x-icon :name="$item['icon']" class="h-5 w-5" />
                            @endif
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-2xl font-bold text-[#101114]" style="font-family: 'IBM Plex Sans', sans-serif;">{{ $item['value'] }}</p>
                            <p class="truncate text-sm text-[#686b82]">{{ $item['label'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        {{-- Main Content Grid --}}
        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(360px,0.82fr)]">
            <div class="space-y-5">
                {{-- Recent Activity --}}
                <section class="rounded-2xl border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-bold text-[#101114]" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -0.5px;">{{ __('Recent activity') }}</h2>
                        <a href="{{ route('calendar') }}" wire:navigate class="inline-flex items-center text-sm font-semibold text-[#7132f5] hover:text-[#5741d8]">
                            {{ __('View all') }}
                            <x-icon name="chevron-right" class="ml-1 h-3.5 w-3.5" />
                        </a>
                    </div>

                    <div class="mt-4 divide-y divide-[#dedee5]">
                        @forelse($activityItems as $item)
                            <a href="{{ $item['route'] }}" wire:navigate class="group flex items-center gap-4 py-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[12px] text-white"
                                    style="background-color: {{ $item['color'] }};">
                                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate font-semibold text-[#101114] group-hover:text-[#7132f5]">{{ $item['title'] }}</span>
                                    <span class="mt-1 block truncate text-sm text-[#686b82]">{{ $item['meta'] }}</span>
                                </span>
                                <span class="shrink-0 text-sm text-[#9497a9]">{{ $item['time'] }}</span>
                            </a>
                        @empty
                            <div class="py-10 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5]">
                                    <x-icon name="check" class="h-5 w-5" />
                                </div>
                                <p class="mt-3 font-semibold text-[#101114]">{{ __('Nothing urgent right now') }}</p>
                                <p class="mt-1 text-sm text-[#9497a9]">{{ __('Upcoming classwork will appear here.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Classrooms --}}
                <section class="rounded-2xl border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-bold text-[#101114]" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -0.5px;">{{ $isStudent ? __('Your classrooms') : __('Top classrooms') }}</h2>
                        <a href="{{ route('classrooms') }}" wire:navigate class="inline-flex items-center text-sm font-semibold text-[#7132f5] hover:text-[#5741d8]">
                            {{ __('View all') }}
                            <x-icon name="chevron-right" class="ml-1 h-3.5 w-3.5" />
                        </a>
                    </div>

                    <div class="mt-4 divide-y divide-[#dedee5]">
                        @forelse($classrooms->take(4) as $classroom)
                            <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="group flex items-center gap-4 py-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[12px] text-white"
                                    style="background-color: {{ $classroom->themeCategory?->color ?? '#7132f5' }};">
                                    <x-icon name="book-open" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate font-semibold text-[#101114] group-hover:text-[#7132f5]">{{ $classroom->name }}</span>
                                    <span class="mt-1 block truncate text-sm text-[#686b82]">
                                        {{ $classroom->section ?: __('No section') }}
                                    </span>
                                </span>
                                <span class="shrink-0 rounded-[6px] bg-[rgba(133,91,251,0.16)] px-3 py-1 text-xs font-semibold text-[#7132f5]">
                                    {{ $classroom->students_count ?? $classroom->students()->count() }} {{ __('students') }}
                                </span>
                            </a>
                        @empty
                            <div class="py-10 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5]">
                                    <x-icon name="academic-cap" class="h-5 w-5" />
                                </div>
                                <p class="mt-3 font-semibold text-[#101114]">{{ __('No classrooms yet') }}</p>
                                <p class="mt-1 text-sm text-[#9497a9]">{{ __('Your classes will show here once you join or create one.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="space-y-5">
                {{-- Upcoming Assignments --}}
                <section class="rounded-2xl border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                    <h2 class="text-xl font-bold text-[#101114]" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -0.5px;">{{ __('Upcoming') }}</h2>
                    <div class="mt-4 space-y-3">
                        @forelse($upcomingAssignments->take(5) as $assignment)
                            <a href="{{ route('assignment.show', [$assignment->classroom, $assignment]) }}" wire:navigate
                                class="block rounded-[12px] border border-[#dedee5] bg-white p-4 transition-all duration-300 hover:border-[rgba(113,50,245,0.3)] hover:bg-[rgba(133,91,251,0.04)]">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-[#101114]">{{ $assignment->title }}</p>
                                        <p class="mt-1 truncate text-sm text-[#686b82]">{{ $assignment->classroom?->name }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-[6px] bg-[rgba(133,91,251,0.16)] px-2.5 py-1 text-xs font-semibold text-[#7132f5]">
                                        {{ $assignment->due_date?->translatedFormat('j M') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-[12px] border border-dashed border-[#dedee5] bg-[rgba(133,91,251,0.04)] px-5 py-8 text-center text-sm text-[#9497a9]">
                                {{ __('No upcoming assignments.') }}
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- CTA Section --}}
                <section class="overflow-hidden rounded-2xl border border-[#5741d8] bg-[#7132f5] p-5 text-white">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[12px] bg-white/15">
                            <x-icon name="academic-cap" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold" style="font-family: 'IBM Plex Sans', sans-serif;">{{ __('Keep shipping progress') }}</h2>
                            <p class="mt-2 text-sm leading-6 text-purple-100">
                                {{ $isStudent ? __('Finish one task, check your class stream, and keep your streak alive.') : __('Review the oldest submissions first, then publish the next class update.') }}
                            </p>
                            <a href="{{ $primaryClassroomRoute }}" wire:navigate
                                class="mt-4 inline-flex items-center gap-2 rounded-[10px] bg-white px-4 py-2 text-sm font-semibold text-[#7132f5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition hover:bg-purple-50">
                                {{ __('Open workspace') }}
                                <x-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </section>


    </div>
</div>
