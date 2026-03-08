@section('page-title', "Achievements")

<div class="px-6 py-6 max-w-7xl mx-auto space-y-8 animate__animated animate__fadeIn">

    <!-- Title -->
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center gap-2">
            <span>Achievements</span>
        </h1>
        <p class="text-gray-400 mt-1 text-sm sm:text-base">Track your progress and earn rewards</p>
    </div>

    <!-- Achievements Grid: 4 per row, horizontal cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($allAchievements as $ach)
            @php
                $isUnlocked = in_array($ach->id, $unlockedAchievementIds);
                $imgSrc = $ach->badge_image ?: 'images/achievements/achievements-img-01.svg';
            @endphp
            <div
                class="card-3d {{ $isUnlocked ? 'card-3d--purple' : 'card-3d--purple' }} flex items-center gap-4 px-4 py-4">
                <!-- Icon Left -->
                <div class="relative shrink-0 w-16 h-16">
                    <img src="{{ asset($imgSrc) }}" alt="{{ $ach->name }}"
                        class="w-full h-full object-contain transition-all duration-300 {{ $isUnlocked ? '' : 'grayscale opacity-40' }}">
                </div>

                <!-- Content Right -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm leading-snug mb-1 {{ $isUnlocked ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $ach->name }}
                    </h3>
                    <p class="text-xs text-gray-400 leading-snug line-clamp-2 mb-2">{{ $ach->description }}</p>

                    <!-- Rewards / Status -->
                    <div class="flex flex-wrap gap-1.5">
                        @if($isUnlocked)
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-full border border-green-200">
                                <i class="fas fa-check-circle text-green-500"></i> {{ __('achievements.unlocked') }}
                            </span>
                        @else
                            @if($ach->coin_reward > 0)
                                <span
                                    class="inline-flex items-center gap-0.5 text-[11px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">
                                    <i class="gsi-gemstone-blue text-blue-400 text-xs"></i> {{ $ach->coin_reward }}
                                </span>
                            @endif
                            @if($ach->xp_reward > 0)
                                <span
                                    class="inline-flex items-center gap-0.5 text-[11px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">
                                    <i class="gsi-flash-lime text-green-400 text-xs"></i> {{ $ach->xp_reward }} XP
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>