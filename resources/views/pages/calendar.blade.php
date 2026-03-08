@extends('layouts.app')
@section('page-title', __('Calendar'))
@section('content')
    <div class="animate__animated animate__fadeIn max-w-4xl mx-auto" style="zoom: {{ auth()->user()->ui_scale }}%;">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 border border-indigo-200 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-indigo-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Calendar') }}</h2>
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
            <div class="card-3d rounded-2xl p-16 text-center">
                <div class="w-16 h-16 bg-indigo-100 border border-indigo-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-indigo-600 text-2xl"></i>
                </div>
                <p class="text-gray-500">{{ __('No upcoming deadlines!') }}</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($upcoming->groupBy(fn($a) => $a->due_date->format('Y-m-d')) as $date => $assignments)
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#6366f1]">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}
                            </span>
                            <span class="text-sm font-medium text-gray-500">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('j F Y') }}
                            </span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-xs text-gray-500 bg-white border border-gray-200 rounded-full px-2 py-0.5">
                                {{ $assignments->count() }} {{ __('item') }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($assignments as $a)
                                @php
                                    $isUrgent = $a->due_date->lt(now()->addDay());
                                    $themeColor = $a->classroom->themeCategory?->color ?? '#8B5CF6';
                                    $themeBg = $themeColor.'12';
                                    $themeBorder = $themeColor.'33';
                                @endphp
                                <a href="{{ route('assignment.show', ['classroom' => $a->classroom, 'assignment' => $a]) }}"
                                   class="card-3d rounded-xl flex items-center gap-4 p-4 transition-colors duration-150 group {{ $isUrgent ? 'border-red-200 bg-red-50/80 hover:bg-red-100/70' : 'hover:bg-gray-50' }}"
                                   style="border-color: {{ $isUrgent ? '#fecaca' : $themeBorder }}; background-color: {{ $isUrgent ? '#fef2f2' : $themeBg }};">
                                    <div class="w-3 h-3 rounded-full shrink-0 ring-2 ring-white"
                                         style="background-color: {{ $themeColor }}"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $a->title }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">
                                            {{ $a->classroom->name }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-medium {{ $isUrgent ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $a->due_date->translatedFormat('H:i') }}
                                        </span>
                                        @if($isUrgent)
                                            <p class="text-xs text-red-500 mt-0.5">{{ __('Due soon') }}</p>
                                        @endif
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
