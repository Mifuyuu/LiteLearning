@extends('layouts.app')
@section('page-title', __('Calendar'))
@section('content')
    <div class="animate__animated animate__fadeIn max-w-4xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-indigo-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-[#e2e8f0]">{{ __('Calendar') }}</h2>
        </div>

        @php
            $classrooms = auth()->user()->allClassrooms();
            $upcoming = collect();
            foreach ($classrooms as $c) {
                $assignments = $c->assignments()->published()
                    ->with('classroom')
                    ->where('due_date', '>=', now())
                    ->orderBy('due_date')
                    ->get();
                $upcoming = $upcoming->merge($assignments);
            }
            $upcoming = $upcoming->sortBy('due_date')->take(20);
        @endphp

        @if($upcoming->isEmpty())
            <div class="rounded-2xl border border-[#1e2d45] bg-[#111827] p-16 text-center">
                <div class="w-16 h-16 bg-indigo-500/10 border border-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-indigo-400 text-2xl"></i>
                </div>
                <p class="text-[#94a3b8]">{{ __('No upcoming deadlines!') }}</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($upcoming->groupBy(fn($a) => $a->due_date->format('Y-m-d')) as $date => $assignments)
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#6366f1]">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}
                            </span>
                            <span class="text-sm font-medium text-[#94a3b8]">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('j F Y') }}
                            </span>
                            <div class="flex-1 h-px bg-[#1e2d45]"></div>
                            <span class="text-xs text-[#475569] bg-[#1e2d45] rounded-full px-2 py-0.5">
                                {{ $assignments->count() }} {{ __('item') }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($assignments as $a)
                                @php
                                    $isUrgent = $a->due_date->lt(now()->addDay());
                                @endphp
                                <a href="{{ route('assignment.show', ['classroom' => $a->classroom, 'assignment' => $a]) }}"
                                   class="flex items-center gap-4 p-4 rounded-xl border transition-all duration-150 group
                                       {{ $isUrgent
                                           ? 'bg-red-950/30 border-red-800/40 hover:bg-red-950/50 hover:border-red-700/50'
                                           : 'bg-[#111827] border-[#1e2d45] hover:bg-[#1e2d45]/60 hover:border-[#2d3f5a]' }}">
                                    <div class="w-3 h-3 rounded-full shrink-0 ring-2 ring-white/10"
                                         style="background-color: {{ $a->classroom->theme_color }}"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-[#e2e8f0] truncate group-hover:text-white transition-colors">
                                            {{ $a->title }}
                                        </p>
                                        <p class="text-xs text-[#64748b] truncate mt-0.5">
                                            {{ $a->classroom->name }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-medium {{ $isUrgent ? 'text-red-400' : 'text-[#94a3b8]' }}">
                                            {{ $a->due_date->translatedFormat('H:i') }}
                                        </span>
                                        @if($isUrgent)
                                            <p class="text-xs text-red-500 mt-0.5">{{ __('Due soon') }}</p>
                                        @endif
                                    </div>
                                    <i class="fas fa-chevron-right text-[#475569] text-xs group-hover:text-[#94a3b8] transition-colors"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
