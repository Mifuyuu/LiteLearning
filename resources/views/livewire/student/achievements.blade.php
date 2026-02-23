@section('page-title', __('achievements.title'))

<div class="space-y-8 animate__animated animate__fadeIn">

    <!-- Title -->
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>{{ __('achievements.title') }}</span>
        </h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">{{ __('achievements.subtitle') }}</p>
    </div>

    <!-- Badges Section -->
    <div>
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="gsi-star-pirple mr-2 text-indigo-500"></i> {{ __('achievements.badges_section') }}
        </h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 sm:gap-4">
            @foreach($allBadges as $badge)
                @php
                    $isEarned = in_array($badge->id, $earnedBadgeIds);
                @endphp
                <div
                    class="bg-white rounded-xl border {{ $isEarned ? 'border-indigo-200 shadow-sm' : 'border-gray-200 opacity-60' }} p-3 sm:p-4 flex flex-col items-center text-center transition-all hover:opacity-100 {{ $isEarned ? 'hover:shadow-md hover:border-indigo-300' : '' }}">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 rounded-full {{ $isEarned ? 'bg-indigo-50' : 'bg-gray-100' }} flex items-center justify-center mb-2 sm:mb-3 text-2xl sm:text-3xl">
                        <i class="{{ $badge->icon }} {{ $isEarned ? $badge->color : 'text-gray-400' }}"></i>
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm text-gray-900 leading-tight mb-1">{{ __($badge->name) }}</h3>
                    <p class="text-xs text-gray-500 hidden sm:block">{{ __($badge->description) }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Achievements Section -->
    <div>
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="gsi-cup-gold mr-2 text-amber-500"></i> {{ __('achievements.achievements_section') }}
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($allAchievements as $ach)
                @php
                    $isUnlocked = in_array($ach->id, $unlockedAchievementIds);
                @endphp
                <div
                    class="flex items-start p-4 sm:p-5 bg-white rounded-xl border transition-all {{ $isUnlocked ? 'border-amber-200 bg-amber-50/20 hover:shadow-md' : 'border-gray-200 hover:border-gray-300' }}">
                    <div
                        class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl {{ $isUnlocked ? 'bg-gradient-to-br from-amber-100 to-amber-200 text-amber-600 shadow-inner' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center text-xl sm:text-2xl">
                        <i class="{{ $ach->icon }}"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 flex-grow min-w-0">
                        <h3
                            class="font-bold text-sm sm:text-base {{ $isUnlocked ? 'text-gray-900' : 'text-gray-600' }} leading-snug">
                            {{ __($ach->name) }}
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 leading-snug mt-1">{{ __($ach->description) }}</p>

                        <div class="mt-2 sm:mt-3 flex flex-wrap items-center gap-2">
                            @if($isUnlocked)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                    <i class="fas fa-check-circle mr-1.5"></i> {{ __('achievements.unlocked') }}
                                </span>
                            @else
                                @if($ach->coin_reward > 0)
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded">
                                        <i class="gsi-gemstone-blue text-blue-500 text-sm"></i> {{ $ach->coin_reward }}
                                    </span>
                                @endif
                                @if($ach->xp_reward > 0)
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded">
                                        <i class="gsi-flash-lime text-green-500 text-sm"></i>
                                        {{ $ach->xp_reward }} XP
                                    </span>
                                @endif
                                <span class="inline-flex items-center text-xs font-medium text-gray-400">
                                    <i class="fas fa-lock text-[10px] mr-1"></i> {{ __('achievements.locked') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>