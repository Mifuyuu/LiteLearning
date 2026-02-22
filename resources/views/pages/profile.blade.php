@extends('layouts.app')
@section('page-title', __('Profile'))
@section('content')
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Profile') }}</h2>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <!-- Cover -->
            <div class="h-32 bg-gradient-to-r from-indigo-600 to-purple-600"></div>

            <div class="px-6 pb-6">
                <!-- Avatar -->
                <div class="-mt-12 mb-4">
                    <img src="{{ auth()->user()->avatar_url }}"
                        class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                </div>

                <h3 class="text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                <p class="text-gray-500">{{ auth()->user()->email }}</p>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mt-2 capitalize">
                    {{ __(ucfirst(auth()->user()->role)) }}
                </span>

                @if(auth()->user()->bio)
                    <p class="mt-4 text-sm text-gray-600">{{ auth()->user()->bio }}</p>
                @endif

                <div class="mt-6 grid grid-cols-1 gap-4">
                    @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->ownedClassrooms()->count() }}</p>
                            <p class="text-sm text-gray-500">{{ __('Classes Teaching') }}</p>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->enrolledClassrooms()->count() }}</p>
                            <p class="text-sm text-gray-500">{{ __('Classes Enrolled') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection