@section('page-title', 'ความสำเร็จ')

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- Header --}}
        <div class="grid gap-6 p-6 lg:grid-cols-[1fr_320px] lg:p-8">
            <div class="space-y-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'ความสำเร็จ' }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#686b82]">
                        {{ 'ทำกิจกรรมการเรียนรู้เพื่อปลดล็อคความสำเร็จ' }}
                    </p>
                </div>
            </div>

            <div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#9497a9]">{{ 'ปลดล็อคแล้ว' }}</p>
                        <p class="mt-1 text-3xl font-black text-[#101114]">{{ $unlockedCount }}/{{ $totalCount }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-[10px] bg-[var(--ll-blue)] text-white shadow-sm">
                        <x-icon name="trophy" class="h-6 w-6" />
                    </div>
                </div>
                <div class="mt-4 h-3 overflow-hidden rounded-full bg-[rgba(59,130,246,0.08)]">
                    <div class="h-full rounded-full bg-[var(--ll-blue)]" style="width: {{ $completionPercent }}%;"></div>
                </div>
                <p class="mt-2 text-xs font-semibold text-[#9497a9]">{{ $completionPercent }}% {{ 'เสร็จสมบูรณ์' }}</p>
            </div>
        </div>

        <div class="border-t border-[#dedee5]"></div>

        {{-- Achievements grid --}}
        <div class="p-6 lg:p-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse($allAchievements as $achievement)
                    @php
                        $isUnlocked = isset($unlockedLookup[$achievement->id]);
                        $imgSrc = $achievement->badge_image ?: 'images/achievements/Achievements_Novice.svg';
                    @endphp
                    <article class="rounded-xl border p-5 transition shadow-[rgba(0,0,0,0.03)_0px_4px_24px] {{ $isUnlocked ? 'border-[#dedee5] bg-white' : 'border-[rgba(104,107,130,0.24)] bg-[rgba(104,107,130,0.04)] opacity-75' }}">
                        <div class="flex gap-4">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg border bg-white {{ $isUnlocked ? 'border-[#dedee5]' : 'border-[rgba(104,107,130,0.24)] grayscale' }}">
                                <img src="{{ asset($imgSrc) }}" alt="{{ $achievement->name }}" class="h-14 w-14 object-contain {{ $isUnlocked ? '' : 'opacity-45' }}">
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <h2 class="line-clamp-2 text-base font-black text-[#101114]">{{ $achievement->name }}</h2>
                                    @if($isUnlocked)
                                        <span class="inline-flex shrink-0 items-center gap-1 rounded-[6px] bg-[var(--ll-blue-subtle)] px-2 py-1 text-[11px] font-black text-[var(--ll-blue)]">
                                            <x-icon name="check-circle" class="h-4 w-4" />
                                            {{ 'ปลดล็อคแล้ว' }}
                                        </span>
                                    @else
                                        <span class="inline-flex shrink-0 items-center gap-1 rounded-[8px] bg-[rgba(104,107,130,0.12)] px-2 py-1 text-[11px] font-black text-[#9497a9]">
                                            <x-icon name="clock" class="h-4 w-4" />
                                            {{ 'ยังไม่ปลดล็อค' }}
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
                                        <span class="inline-flex items-center gap-1 rounded-[6px] bg-[var(--ll-blue-subtle)] px-2.5 py-1 text-[var(--ll-blue)]">
                                            +{{ $achievement->xp_reward }}
                                            <x-icon name="bolt" class="h-3.5 w-3.5 text-[var(--ll-blue)]" />
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
        </div>

    </div>
</div>
