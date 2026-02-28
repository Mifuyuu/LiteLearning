@extends('layouts.app')
@section('page-title', __('Calendar'))
@section('content')
    <div class="animate__animated animate__fadeIn max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Calendar') }}</h2>

        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-alt text-indigo-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('Calendar View') }}</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                {{ __('View all your upcoming assignments and deadlines in one place.') }}
            </p>

            <!-- Upcoming assignments list -->
            <div class="mt-8 text-left">
                @php
                    $classrooms = auth()->user()->allClassrooms();
                    $upcoming = collect();
                    foreach ($classrooms as $c) {
                        $assignments = $c->assignments()->published()->where('due_date', '>=', now())->orderBy('due_date')->get();
                        $upcoming = $upcoming->merge($assignments);
                    }
                    $upcoming = $upcoming->sortBy('due_date')->take(20);
                @endphp

                @forelse($upcoming->groupBy(fn($a) => $a->due_date->format('Y-m-d')) as $date => $assignments)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-500 mb-2">
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('l, j F') }}
                        </h4>
                        <div class="space-y-2">
                            @foreach($assignments as $a)
                                @php
                                    $isUrgent = $a->due_date->lt(now()->addDay());
                                @endphp
                <a href="{{ route('assignment.show', ['classroom' => $a->getClassroom(), 'assignment' => $a]) }}"
                                    class="block p-3 rounded-lg border transition-colors {{ $isUrgent ? 'bg-red-50 border-red-200 hover:bg-red-100' : 'bg-gray-50 border-gray-200 hover:bg-gray-100' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full mr-3"
                                style="background-color: {{ $a->getClassroom()->theme_color }}"></div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $a->title }}</p>
                                <p class="text-xs text-gray-500">{{ $a->getClassroom()->name }}</p>
                                            </div>
                                        </div>
                                        <span
                                            class="text-xs {{ $isUrgent ? 'text-red-500' : 'text-gray-400' }}">{{ $a->due_date->translatedFormat('H:i') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-4">{{ __('No upcoming deadlines!') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection