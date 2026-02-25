@section('page-title', __('Leaderboard'))

<div class="max-w-4xl mx-auto space-y-4 animate__animated animate__fadeIn">

    {{-- Header --}}
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
            {{ __('Leaderboard') }}
        </h1>
        <p class="text-sm text-gray-400 mt-1">{{ __('Top students ranked by XP') }}</p>
    </div>

    @if($topStudents->isNotEmpty())

        {{-- Podium: Top 3 --}}
        <div class="bg-white rounded-2xl border border-gray-200 px-6 pt-8 pb-0 overflow-hidden">

            {{-- Podium layout: 2nd | 1st | 3rd --}}
            <div class="flex items-end justify-center gap-3">

                {{-- 2nd Place --}}
                @if($topStudents->has(1))
                    <div class="flex flex-col items-center w-1/3 max-w-[160px]">
                        <div class="relative inline-block mb-2">
                            <img src="{{ $topStudents[1]->user->avatar_url }}"
                                class="w-14 h-14 rounded-full object-cover ring-2 ring-indigo-200 shadow bg-white">
                            @if($topStudents[1]->user->active_avatar_frame && !str_starts_with($topStudents[1]->user->active_avatar_frame, 'border'))
                                <img src="{{ asset($topStudents[1]->user->active_avatar_frame) }}" 
                                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                            @elseif($topStudents[1]->user->active_avatar_frame)
                                <div class="absolute inset-0 rounded-full {{ $topStudents[1]->user->active_avatar_frame }} pointer-events-none"></div>
                            @endif
                        </div>
                        <p
                            class="text-sm font-semibold text-gray-700 truncate w-full text-center mb-1 {{ $topStudents[1]->user->active_name_color ?? '' }}">
                            {{ $topStudents[1]->user->name }}</p>
                        <div class="flex items-center gap-1 text-xs text-gray-500 mb-3">
                            <i class="gsi-flash-lime text-green-500 text-sm"></i>
                            <span class="font-bold text-gray-600">{{ number_format($topStudents[1]->xp) }}</span>
                        </div>
                        {{-- Podium block --}}
                        <div class="w-full h-[100px] bg-indigo-300 rounded-t-xl flex flex-col items-center justify-center gap-1 shadow-inner animate__animated animate__slideInUp" style="animation-delay: 0.2s;">
                            <span class="text-2xl font-black text-indigo-900">2</span>
                            <span class="text-base text-indigo-900/70">{{ __('Lv.') }} {{ $topStudents[1]->level }}</span>
                        </div>
                    </div>
                @endif

                {{-- 1st Place --}}
                @if($topStudents->has(0))
                    <div class="flex flex-col items-center w-1/3 max-w-[180px]">
                        <i class="fas fa-crown text-amber-400 text-lg mb-1 animate__animated animate__bounceIn animate__delay-1s"></i>
                        <div class="relative inline-block mb-2">
                            <img src="{{ $topStudents[0]->user->avatar_url }}"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover ring-2 ring-amber-300 shadow-md bg-white">
                            @if($topStudents[0]->user->active_avatar_frame && !str_starts_with($topStudents[0]->user->active_avatar_frame, 'border'))
                                <img src="{{ asset($topStudents[0]->user->active_avatar_frame) }}" 
                                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                            @elseif($topStudents[0]->user->active_avatar_frame)
                                <div class="absolute inset-0 rounded-full {{ $topStudents[0]->user->active_avatar_frame }} pointer-events-none"></div>
                            @endif
                        </div>
                        <p
                            class="text-sm sm:text-base font-bold text-gray-800 truncate w-full text-center mb-1 {{ $topStudents[0]->user->active_name_color ?? '' }}">
                            {{ $topStudents[0]->user->name }}</p>
                        <div class="flex items-center gap-1 text-xs text-gray-500 mb-3">
                            <i class="gsi-flash-lime text-green-500 text-sm"></i>
                            <span class="font-bold text-gray-700">{{ number_format($topStudents[0]->xp) }}</span>
                        </div>
                        {{-- Podium block --}}
                        <div
                            class="w-full h-[145px] bg-indigo-600 rounded-t-xl flex flex-col items-center justify-center gap-1 shadow-inner animate__animated animate__slideInUp" style="animation-delay: 0.4s;">
                            <span class="text-3xl font-black text-white">1</span>
                            <span class="text-base text-indigo-200">{{ __('Lv.') }} {{ $topStudents[0]->level }}</span>
                        </div>
                    </div>
                @endif

                {{-- 3rd Place --}}
                @if($topStudents->has(2))
                    <div class="flex flex-col items-center w-1/3 max-w-[160px]">
                        <div class="relative inline-block mb-2">
                            <img src="{{ $topStudents[2]->user->avatar_url }}"
                                class="w-14 h-14 rounded-full object-cover ring-2 ring-orange-200 shadow bg-white">
                            @if($topStudents[2]->user->active_avatar_frame && !str_starts_with($topStudents[2]->user->active_avatar_frame, 'border'))
                                <img src="{{ asset($topStudents[2]->user->active_avatar_frame) }}" 
                                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                            @elseif($topStudents[2]->user->active_avatar_frame)
                                <div class="absolute inset-0 rounded-full {{ $topStudents[2]->user->active_avatar_frame }} pointer-events-none"></div>
                            @endif
                        </div>
                        <p
                            class="text-sm font-semibold text-gray-700 truncate w-full text-center mb-1 {{ $topStudents[2]->user->active_name_color ?? '' }}">
                            {{ $topStudents[2]->user->name }}</p>
                        <div class="flex items-center gap-1 text-xs text-gray-500 mb-3">
                            <i class="gsi-flash-lime text-green-500 text-sm"></i>
                            <span class="font-bold text-gray-600">{{ number_format($topStudents[2]->xp) }}</span>
                        </div>
                        {{-- Podium block --}}
                        <div class="w-full h-[72px] bg-indigo-100 rounded-t-xl flex flex-col items-center justify-center gap-1 animate__animated animate__slideInUp" style="animation-delay: 0s;">
                            <span class="text-2xl font-black text-indigo-900">3</span>
                            <span class="text-base text-indigo-900/70">{{ __('Lv.') }} {{ $topStudents[2]->level }}</span>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Rank 4+ --}}
        @if($topStudents->count() > 3)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                @foreach($topStudents->skip(3)->values() as $i => $record)
                    @php $rank = $i + 4;
                    $isMe = auth()->id() === $record->user_id; @endphp
                    <div
                        class="flex items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }} {{ $isMe ? 'bg-indigo-50/60' : 'hover:bg-gray-50' }} transition-colors">
                        <span class="text-sm font-bold text-gray-300 w-6 text-center shrink-0">{{ $rank }}</span>
                        <div class="relative inline-block shrink-0">
                            <img src="{{ $record->user->avatar_url }}"
                                class="w-9 h-9 rounded-full object-cover border border-gray-100 bg-white">
                            @if($record->user->active_avatar_frame && !str_starts_with($record->user->active_avatar_frame, 'border'))
                                <img src="{{ asset($record->user->active_avatar_frame) }}" 
                                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                            @elseif($record->user->active_avatar_frame)
                                <div class="absolute inset-0 rounded-full {{ $record->user->active_avatar_frame }} pointer-events-none"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate {{ $record->user->active_name_color ?? '' }}">
                                {{ $record->user->name }}
                                @if($isMe)
                                    <span
                                        class="ml-1 text-[10px] font-bold text-indigo-500 bg-indigo-100 px-1.5 py-0.5 rounded">YOU</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400">{{ __('Lv.') }} {{ $record->level }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <i class="gsi-flash-lime text-green-500 text-sm"></i>
                            <span class="text-sm font-bold text-gray-600">{{ number_format($record->xp) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @else
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
            <i class="fas fa-trophy text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-gray-400 text-sm">{{ __('No ranking data available yet.') }}</p>
        </div>
    @endif

</div>