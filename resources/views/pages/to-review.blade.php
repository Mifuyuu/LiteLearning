@extends('layouts.app')
@section('page-title', __('To Review'))
@section('content')
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('To Review') }}</h2>

        @php
            $classrooms = auth()->user()->ownedClassrooms()->where('is_archived', false)->get();
            $pendingSubmissions = collect();
            foreach ($classrooms as $c) {
                foreach ($c->assignments as $a) {
                    $subs = $a->submissions()->where('status', 'turned_in')->with('user', 'assignment.classroom')->get();
                    $pendingSubmissions = $pendingSubmissions->merge($subs);
                }
            }
            $pendingSubmissions = $pendingSubmissions->sortByDesc('turned_in_at');
        @endphp

        @if($pendingSubmissions->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('All caught up!') }}</h3>
                <p class="text-gray-500">{{ __('No pending submissions to review.') }}</p>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                @foreach($pendingSubmissions as $sub)
                    <a href="{{ route('assignment.grade', ['classroom' => $sub->assignment->classroom, 'assignment' => $sub->assignment, 'submission' => $sub]) }}"
                        class="block p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="{{ $sub->user->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $sub->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $sub->assignment->title }} &middot;
                                        {{ $sub->assignment->classroom?->name }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-blue-600 font-medium">{{ __('Needs review') }}</span>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $sub->turned_in_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
