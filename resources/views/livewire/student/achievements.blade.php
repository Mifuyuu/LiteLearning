@section('page-title', __('Achievements'))

<div class="space-y-6">
    <section class="overflow-hidden rounded-2xl border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="grid gap-6 p-6 lg:grid-cols-[1fr_320px] lg:p-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 rounded-[8px] border border-[#dedee5] bg-[rgba(133,91,251,0.16)] px-3 py-1 text-xs font-black uppercase tracking-[0.18em] text-[#7132f5]">
                    <x-icon name="sparkles" class="h-4 w-4" />
                    {{ __('Progress Board') }}
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ __('Achievements') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#686b82]">
                        {{ __('Complete learning actions to unlock badges, coins, and XP rewards.') }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-[#dedee5] bg-[rgba(133,91,251,0.04)] p-5">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#9497a9]">{{ __('Unlocked') }}</p>
                        <p class="mt-1 text-3xl font-black text-[#101114]">{{ $unlockedCount }}/{{ $totalCount }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-[10px] bg-[#7132f5] text-white shadow-sm">
                        <x-icon name="trophy" class="h-6 w-6" />
                    </div>
                </div>
                <div class="mt-4 h-3 overflow-hidden rounded-full bg-white border border-[#dedee5]">
                    <div class="h-full rounded-full bg-[#7132f5]" style="width: {{ $completionPercent }}%;"></div>
                </div>
                <p class="mt-2 text-xs font-semibold text-[#9497a9]">{{ $completionPercent }}% {{ __('complete') }}</p>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($allAchievements as $achievement)
            @php
                $isUnlocked = isset($unlockedLookup[$achievement->id]);
                $imgSrc = $achievement->badge_image ?: 'images/achievements/achievements-img-01.svg';
            @endphp
            <article class="rounded-2xl border p-5 transition shadow-[rgba(0,0,0,0.03)_0px_4px_24px] {{ $isUnlocked ? 'border-[#dedee5] bg-white' : 'border-[rgba(104,107,130,0.24)] bg-[rgba(104,107,130,0.04)] opacity-75' }}">
                <div class="flex gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-[12px] border {{ $isUnlocked ? 'border-[#dedee5] bg-[rgba(133,91,251,0.16)]' : 'border-[rgba(104,107,130,0.24)] bg-white grayscale' }}">
                        <img src="{{ asset($imgSrc) }}" alt="{{ $achievement->name }}" class="h-12 w-12 object-contain {{ $isUnlocked ? '' : 'opacity-45' }}">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="line-clamp-2 text-base font-black text-[#101114]">{{ $achievement->name }}</h2>
                            @if($isUnlocked)
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-[6px] bg-[rgba(133,91,251,0.16)] px-2 py-1 text-[11px] font-black text-[#7132f5]">
                                    <x-icon name="check-circle" class="h-4 w-4" />
                                    {{ __('Unlocked') }}
                                </span>
                            @else
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-[8px] bg-[rgba(104,107,130,0.12)] px-2 py-1 text-[11px] font-black text-[#9497a9]">
                                    <x-icon name="clock" class="h-4 w-4" />
                                    {{ __('Locked') }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-[#686b82]">{{ $achievement->description }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-black">
                            @if($achievement->coin_reward > 0)
                                <span class="inline-flex items-center gap-1 rounded-[6px] bg-amber-50 px-2.5 py-1 text-amber-600">
                                    +{{ $achievement->coin_reward }}
                                    <x-icon name="star-solid" class="h-3.5 w-3.5" />
                                </span>
                            @endif
                            @if($achievement->xp_reward > 0)
                                <span class="inline-flex items-center gap-1 rounded-[6px] bg-[rgba(133,91,251,0.16)] px-2.5 py-1 text-[#7132f5]">
                                    +{{ $achievement->xp_reward }}
                                    <x-icon name="bolt" class="h-3.5 w-3.5" />
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#dedee5] bg-white p-8 text-center text-sm text-[#9497a9] sm:col-span-2 xl:col-span-3">
                {{ __('No achievements configured yet.') }}
            </div>
        @endforelse
    </section>
</div>
