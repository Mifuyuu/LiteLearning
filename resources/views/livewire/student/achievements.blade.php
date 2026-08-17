@section('page-title', 'ความสำเร็จ')

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- Header --}}
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/achievements.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-42 w-42 select-none object-contain sm:block lg:right-8 lg:top-8" />

            <div>
                <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'ความสำเร็จ' }}</h1>
                <p class="mt-2 max-w-2xl text-md leading-6 text-[#686b82]">
                    {{ 'ทำกิจกรรมการเรียนรู้เพื่อปลดล็อคความสำเร็จ' }}
                </p>
            </div>

            <div class="mt-6 max-w-md space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-black text-(--ll-blue)">
                        <span class="ml-1 text-md text-[#9497a9]">{{ 'ปลดล็อคแล้ว' }}</span>
                        {{ $unlockedCount }}/{{ $totalCount }}
                    </p>
                    <p class="text-sm font-black text-[#9497a9]">{{ $completionPercent }}% {{ 'เสร็จสมบูรณ์' }}</p>
                </div>
                <div class="dashboard-liquid-progress outline-2 outline-[rgba(37,99,235,0.28)]">
                    <span class="dashboard-liquid-fill" style="width: {{ $completionPercent }}%"></span>
                </div>
            </div>
        </div>

        {{-- Achievements grid --}}
        <div class="p-6 lg:p-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse($allAchievements as $achievement)
                    @php
                        $isUnlocked = isset($unlockedLookup[$achievement->id]);
                        $imgSrc = $achievement->badge_image ?: 'images/achievements/Achievements_Novice.png';
                        $unlocked = $unlockedLookup[$achievement->id] ?? null;
                        $unlockedAt = $unlocked?->pivot->unlocked_at
                            ? $unlocked->pivot->unlocked_at->translatedFormat('j F Y')
                            : null;
                    @endphp
                    <article class="rounded-xl border p-5 transition shadow-[rgba(0,0,0,0.03)_0px_4px_24px] {{ $isUnlocked ? 'border-[#dedee5] bg-white' : 'border-[rgba(104,107,130,0.24)] bg-[rgba(104,107,130,0.04)] opacity-75' }}">
                        <div class="flex gap-4">
                            <div @if($isUnlocked) @click="$dispatch('achievement-show', {{ \Illuminate\Support\Js::from(['name' => $achievement->name, 'description' => $achievement->description, 'badge_image' => $imgSrc, 'unlocked_at' => $unlockedAt]) }})" @endif
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg border-2 bg-slate-100 {{ $isUnlocked ? 'cursor-pointer border-[#dedee5] transition hover:ring-2 hover:ring-[#3293F5]' : 'border-[rgba(104,107,130,0.24)] grayscale' }}">
                                <img src="{{ asset($imgSrc).'?v='.@filemtime(public_path($imgSrc)) }}" alt="{{ $achievement->name }}" class="h-14 w-14 object-contain {{ $isUnlocked ? '' : 'opacity-45' }}">
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <h2 class="line-clamp-2 text-base font-black text-[#101114]">{{ $achievement->name }}</h2>
                                </div>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-[#686b82]">{{ $achievement->description }}</p>
                                <div class="mt-4 flex flex-wrap gap-2 text-xs font-black justify-end">
                                    @if($achievement->coin_reward > 0)
                                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2.5 py-1 text-amber-600">
                                            +{{ $achievement->coin_reward }}
                                            <x-icon name="star-solid" class="h-3.5 w-3.5" />
                                        </span>
                                    @endif
                                    @if($achievement->xp_reward > 0)
                                        <span class="inline-flex items-center gap-1 rounded-md bg-(--ll-blue-subtle) px-2.5 py-1 text-(--ll-blue)">
                                            +{{ $achievement->xp_reward }}
                                            <x-icon name="bolt" class="h-3.5 w-3.5 text-(--ll-blue)" />
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-[#dedee5] bg-white p-8 text-center text-sm text-[#9497a9] col-span-2">
                        {{ 'ยังไม่มีความสำเร็จที่กำหนดค่าไว้' }}
                    </div>
                @endforelse
            </div>

            {{-- Achievement detail modal (opened via $dispatch('achievement-show', {...})) --}}
            <x-achievement-detail-modal />
        </div>

    </div>
</div>
