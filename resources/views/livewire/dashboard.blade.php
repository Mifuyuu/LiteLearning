@section('page-title', __('Dashboard'))

@php
    $isStudent = $role === 'student';
    $displayName = trim(explode(' ', $user->name)[0] ?? $user->name);
    $activityDays = collect($activity['days']);
    $activityGridStart = \Illuminate\Support\Carbon::parse($activity['grid_start_date']);
    $monthLabels = collect(range(1, 12))
        ->map(function (int $month) use ($activity, $activityGridStart): array {
            $date = \Illuminate\Support\Carbon::create($activity['year'], $month, 1);

            return [
                'week' => intdiv((int) $activityGridStart->diffInDays($date), 7) + 1,
                'label' => $date->translatedFormat('M'),
            ];
        });
    $activityLevelClasses = [
        0 => 'bg-[#ece9f0]',
        1 => 'bg-[#e2d4ff]',
        2 => 'bg-[#bea1fb]',
        3 => 'bg-[#9363ef]',
        4 => 'bg-[#7132f5]',
    ];
@endphp

<div data-dashboard-role="{{ $role }}"
    class="flex min-h-full flex-col gap-3 animate__animated animate__fadeIn xl:h-[calc(100vh-3rem)] xl:min-h-[560px] xl:overflow-hidden">
    <header class="flex shrink-0 items-center justify-between gap-4">
        <div class="min-w-0">
            <h1 class="truncate text-2xl font-bold tracking-tight text-[#101114]">
                {{ __('Hello, :name', ['name' => $displayName]) }}
            </h1>
            <p class="mt-0.5 truncate text-sm text-[#686b82]">
                {{ $isStudent ? __('See how your learning is growing today.') : __('Start with the submissions that need your attention.') }}
            </p>
        </div>
        <a href="{{ route('calendar') }}" wire:navigate
            class="inline-flex shrink-0 items-center gap-2 rounded-[10px] bg-white px-3 py-2 text-sm font-semibold text-[#7132f5] transition hover:bg-[rgba(133,91,251,0.08)]">
            <x-icon name="calendar-days" class="h-4 w-4" />
            <span class="hidden sm:inline">{{ __('Calendar') }}</span>
        </a>
    </header>

    <div class="grid min-h-0 flex-1 gap-3 xl:grid-cols-[minmax(220px,0.78fr)_minmax(380px,1.55fr)_minmax(220px,0.78fr)]">
        <div class="grid min-h-0 gap-3 xl:grid-rows-[180px_minmax(0,1fr)]">
            <section class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body gap-0 p-5">
                    @if($isStudent)
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#7132f5]">{{ __('Current Level') }}</p>
                                <p class="mt-2 text-5xl font-bold leading-none text-[#101114]">{{ $primaryMetric['level'] }}</p>
                            </div>
                            <span class="rounded-[9px] bg-[rgba(133,91,251,0.16)] px-2.5 py-1 text-xs font-bold text-[#7132f5]">
                                {{ number_format($primaryMetric['xp_current']) }} / {{ number_format($primaryMetric['xp_required']) }} XP
                            </span>
                        </div>
                        <div class="dashboard-liquid-progress mt-5 outline-2 outline-[rgba(113,50,245,0.28)]" role="progressbar"
                            aria-label="{{ __('Level progress') }}" aria-valuemin="0" aria-valuemax="100"
                            aria-valuenow="{{ $primaryMetric['progress_percent'] }}">
                            <span class="dashboard-liquid-fill" style="width: {{ $primaryMetric['progress_percent'] }}%">
                                <span data-liquid-bubbles class="dashboard-liquid-bubbles" aria-hidden="true">
                                    <span data-liquid-bubble></span>
                                    <span data-liquid-bubble></span>
                                    <span data-liquid-bubble></span>
                                    <span data-liquid-bubble></span>
                                </span>
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-[#686b82]">
                            {{ __(':xp XP until level :level', ['xp' => number_format($primaryMetric['remaining']), 'level' => $primaryMetric['level'] + 1]) }}
                        </p>
                    @else
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#7132f5]">{{ __('Pending Review') }}</p>
                                <p class="mt-2 text-5xl font-bold leading-none text-[#101114]">{{ number_format($primaryMetric['pending']) }}</p>
                            </div>
                            <span class="rounded-[9px] bg-[rgba(133,91,251,0.16)] px-2.5 py-1 text-xs font-bold text-[#7132f5]">
                                {{ number_format($primaryMetric['graded_this_week']) }} {{ __('reviewed') }}
                            </span>
                        </div>
                        <div class="dashboard-liquid-progress mt-5 outline-2 outline-[rgba(113,50,245,0.28)]" role="progressbar"
                            aria-label="{{ __('Review progress') }}" aria-valuemin="0" aria-valuemax="100"
                            aria-valuenow="{{ $primaryMetric['progress_percent'] }}">
                            <span class="dashboard-liquid-fill" style="width: {{ $primaryMetric['progress_percent'] }}%">
                                <span data-liquid-bubbles class="dashboard-liquid-bubbles" aria-hidden="true">
                                    <span data-liquid-bubble></span>
                                    <span data-liquid-bubble></span>
                                    <span data-liquid-bubble></span>
                                    <span data-liquid-bubble></span>
                                </span>
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-[#686b82]">{{ __('Weekly review completion') }} · {{ $primaryMetric['progress_percent'] }}%</p>
                    @endif
                </div>
            </section>

            <section class="card card-border min-h-[330px] overflow-hidden border-[#dedee5] bg-white xl:min-h-0">
                <div class="card-body min-h-0 gap-0 p-4">
                    <div class="flex shrink-0 items-center justify-between gap-3">
                        <h2 class="card-title text-base text-[#101114]">{{ $isStudent ? __('Up Next') : __('Review Queue') }}</h2>
                        <a href="{{ $isStudent ? route('calendar') : route('to-review') }}" wire:navigate
                            class="text-xs font-bold text-[#7132f5] hover:text-[#5741d8]">{{ __('View all') }}</a>
                    </div>
                    <div class="mt-2 min-h-0 divide-y divide-[#ece9f0] overflow-hidden">
                        @forelse($actionItems as $item)
                            <a href="{{ $isStudent ? route('assignment.show', [$item->classroom, $item]) : route('to-review') }}"
                                wire:navigate class="group flex items-center gap-3 py-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] text-white"
                                    style="background-color: {{ $item->classroom?->themeCategory?->color ?? '#7132f5' }}">
                                    <x-icon name="{{ $isStudent ? 'document-text' : 'clipboard-document-list' }}" class="h-4 w-4" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-[#101114] group-hover:text-[#7132f5]">{{ $item->title }}</span>
                                    <span class="mt-0.5 block truncate text-xs text-[#686b82]">{{ $item->classroom?->name }}</span>
                                </span>
                                <span class="shrink-0 rounded-[7px] bg-[rgba(133,91,251,0.12)] px-2 py-1 text-[11px] font-bold text-[#7132f5]">
                                    {{ $isStudent ? $item->due_date?->translatedFormat('j M') : $item->pending_count }}
                                </span>
                            </a>
                        @empty
                            <div class="flex h-full min-h-48 flex-col items-center justify-center text-center">
                                <span class="flex h-11 w-11 items-center justify-center rounded-[12px] bg-[rgba(133,91,251,0.12)] text-[#7132f5]">
                                    <x-icon name="check-circle" class="h-5 w-5" />
                                </span>
                                <p class="mt-3 text-sm font-semibold text-[#101114]">{{ __('Nothing urgent right now') }}</p>
                                <p class="mt-1 text-xs text-[#9497a9]">{{ $isStudent ? __('Upcoming work will appear here.') : __('New submissions will appear here.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <section data-activity-heatmap
            class="card card-border min-h-[510px] overflow-hidden border-[#dedee5] bg-white xl:min-h-0">
            <div class="card-body flex h-full min-h-0 flex-col gap-0 p-4 sm:p-5">
                <div class="flex shrink-0 items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#7132f5]">{{ __('1-year activity') }}</p>
                        <h2 class="card-title mt-1 text-lg text-[#101114]">
                            {{ $isStudent ? __('Learning consistency') : __('Teaching activity') }}
                        </h2>
                        <p class="mt-1 text-xs text-[#686b82]">
                            {{ $isStudent ? __('Submissions, attendance, comments, and grades') : __('Classwork, reviews, and comments') }}
                        </p>
                    </div>
                    <span class="rounded-[9px] bg-[rgba(133,91,251,0.12)] px-2.5 py-1.5 text-xs font-bold text-[#7132f5]">
                        {{ number_format($activity['total']) }} {{ __('activities') }}
                    </span>
                </div>

                <div class="mt-5 min-h-0 flex-1 overflow-x-auto">
                    <div class="grid min-w-[720px] grid-cols-[24px_minmax(0,1fr)] gap-2">
                        <span></span>
                        <div class="grid text-[10px] font-medium text-[#9497a9]"
                            style="grid-template-columns: repeat({{ $activity['week_count'] }}, 0.75rem); gap: 0.25rem;">
                            @foreach($monthLabels as $month)
                                <span class="whitespace-nowrap" style="grid-column: {{ $month['week'] }};">{{ $month['label'] }}</span>
                            @endforeach
                        </div>
                        <div class="grid grid-rows-7 gap-1 text-[9px] text-[#9497a9]">
                            <span>{{ __('Mon') }}</span><span></span><span>{{ __('Wed') }}</span><span></span>
                            <span>{{ __('Fri') }}</span><span></span><span>{{ __('Sun') }}</span>
                        </div>
                        <div class="grid grid-flow-col grid-rows-7 gap-1"
                            style="grid-template-columns: repeat({{ $activity['week_count'] }}, 0.75rem);">
                            @foreach($activityDays as $day)
                                <span data-activity-cell tabindex="0"
                                    class="tooltip tooltip-top aspect-square size-3 rounded-[3px] outline-none ring-[#7132f5] transition hover:ring-2 focus:ring-2 {{ ! $day['is_in_year'] || $day['is_future'] ? 'bg-[#f5f3f7]' : $activityLevelClasses[$day['level']] }}"
                                    data-tip="{{ $day['label'] }} · {{ $day['count'] }} {{ __('activities') }}"
                                    aria-label="{{ $day['label'] }}: {{ $day['count'] }} {{ __('activities') }}"></span>
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

        <div class="grid min-h-0 gap-3 xl:grid-rows-[250px_minmax(0,1fr)]">
            <a href="{{ route('profile') }}" wire:navigate
                class="card card-border group overflow-hidden border-[#dedee5] bg-white transition hover:border-[rgba(113,50,245,0.3)]">
                <div class="card-body items-center gap-0 p-5 text-center">
                    <div class="avatar">
                        <div class="h-20 w-20 rounded-full ring-4 ring-[rgba(133,91,251,0.16)] ring-offset-2 ring-offset-white">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="object-cover">
                        </div>
                    </div>
                    <h2 class="mt-4 max-w-full truncate text-lg font-bold text-[#101114] group-hover:text-[#7132f5]">{{ $user->name }}</h2>
                    <p class="mt-1 text-xs text-[#686b82]">{{ __($isStudent ? 'Student' : 'Teacher') }} · {{ $classrooms->count() }} {{ __('classrooms') }}</p>
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        @if($isStudent)
                            <span class="badge border-0 bg-[rgba(133,91,251,0.16)] text-[#7132f5]">{{ __('Level') }} {{ $primaryMetric['level'] }}</span>
                        @else
                            <span class="badge border-0 bg-[rgba(133,91,251,0.16)] text-[#7132f5]">{{ __('Teacher') }}</span>
                        @endif
                        <span class="badge border-0 bg-[#f2eff5] text-[#686b82]">{{ __('View profile') }}</span>
                    </div>
                </div>
            </a>

            <section class="card card-border min-h-[270px] overflow-hidden border-[#dedee5] bg-white xl:min-h-0">
                <div class="card-body gap-0 p-4">
                    <h2 class="card-title text-base text-[#101114]">{{ __('Quick Stats') }}</h2>
                    <div class="mt-3 grid min-h-0 flex-1 grid-cols-2 gap-2">
                        @foreach($quickStats as $stat)
                            <div class="flex min-h-24 flex-col justify-between rounded-[11px] bg-[#f7f5f9] p-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-[9px] bg-[rgba(133,91,251,0.14)] text-[#7132f5]">
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
