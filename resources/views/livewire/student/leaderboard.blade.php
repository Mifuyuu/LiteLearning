@section('page-title', 'กระดานผู้นำ')

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh_-_3rem)]">
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/trophy.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-[calc(100%_-_2rem)] w-auto select-none object-contain sm:block lg:right-8" />

            <div class="relative max-w-sm">
                <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'กระดานผู้นำ' }}</h1>
                <p class="mt-2 text-md leading-6 text-[#686b82]">{{ 'ไต่อันดับด้วยเลเวลและ XP ของคุณ' }}</p>
            </div>
        </div>

        @if($topStudents->isEmpty())
            <div class="border-t border-[#dedee5] p-16 text-center">
                <x-icon name="trophy" class="h-16 w-16 text-[#dedee5] mb-3 mx-auto" />
                <p class="text-[#9497a9] text-sm">{{ 'ยังไม่มีข้อมูลการจัดอันดับ' }}</p>
            </div>
        @else
            <div class="border-t border-[#dedee5] p-6 lg:p-8">

                {{-- Podium layout: 2nd | 1st | 3rd --}}
                @php
                    $podiumOrder = [1, 0, 2];
                    $podiumConfig = [
                        0 => ['wrap' => 'w-1/3 max-w-[180px]', 'avatar' => 'w-16 h-16 sm:w-20 sm:h-20', 'ring' => 'ring-amber-300', 'name' => 'text-sm sm:text-base font-bold text-[#101114]', 'height' => 'h-[145px]', 'bg' => 'bg-[#FFBF00]', 'levelText' => 'text-black font-bold', 'trophy' => 'Trophy_Golden.png', 'trophySize' => 'h-20 w-20', 'delay' => '0.4s'],
                        1 => ['wrap' => 'w-1/3 max-w-[160px]', 'avatar' => 'w-14 h-14', 'ring' => 'ring-[rgba(37,99,235,0.4)]', 'name' => 'text-sm font-semibold text-[#686b82]', 'height' => 'h-[100px]', 'bg' => 'bg-[#E7EDEE]', 'levelText' => 'text-black font-bold', 'trophy' => 'Trophy_Silver.png', 'trophySize' => 'h-16 w-16', 'delay' => '0.2s'],
                        2 => ['wrap' => 'w-1/3 max-w-[160px]', 'avatar' => 'w-14 h-14', 'ring' => 'ring-[rgba(37,99,235,0.2)]', 'name' => 'text-sm font-semibold text-[#686b82]', 'height' => 'h-[72px]', 'bg' => 'bg-[#F49D5F]', 'levelText' => 'text-black font-bold', 'trophy' => 'Trophy_Bronze.png', 'trophySize' => 'h-14 w-14', 'delay' => null],
                    ];
                @endphp
                <div class="relative overflow-hidden">
                    <div class="achievement-burst" aria-hidden="true"></div>
                    <div class="relative z-10 flex items-end justify-center gap-3">
                    @foreach($podiumOrder as $idx)
                        @continue(!$topStudents->has($idx))
                        @php $c = $podiumConfig[$idx]; $student = $topStudents[$idx]; @endphp
                        <a href="{{ route('profile', $student->user) }}" wire:navigate class="flex flex-col items-center {{ $c['wrap'] }}">
                            <div class="relative inline-block mb-2">
                                <img src="{{ $student->user->avatar_url }}"
                                    class="{{ $c['avatar'] }} rounded-full object-cover ring-2 {{ $c['ring'] }} bg-white">
                                @if($student->user->active_avatar_frame && !str_starts_with($student->user->active_avatar_frame, 'border'))
                                    <img src="{{ asset($student->user->active_avatar_frame) }}"
                                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                @elseif($student->user->active_avatar_frame)
                                    <div class="absolute inset-0 rounded-full {{ $student->user->active_avatar_frame }} pointer-events-none"></div>
                                @endif
                            </div>
                            <div class="flex items-center justify-center gap-1 w-full mb-1">
                                <p class="{{ $c['name'] }} truncate {{ $student->user->active_name_color ?? '' }}">
                                    {{ $student->user->name }}</p>
                                @foreach($student->user->displayed_badges as $badge)
                                    <img src="{{ asset($badge->badge_image ?: 'images/achievements/Achievements_Novice.png') }}"
                                        alt="{{ $badge->name }}" title="{{ $badge->name }}" class="h-4 w-4 shrink-0 object-contain">
                                @endforeach
                            </div>
                            <div class="flex items-center gap-1 text-xs text-[#9497a9] mb-3">
                                <span class="font-bold text-[#686b82]">{{ number_format($student->xp) }}</span>
                                <x-icon name="bolt" class="text-(--ll-blue) h-4 w-4 shrink-0" />
                            </div>
                            {{-- Podium block --}}
                            <img src="{{ asset('images/'.$c['trophy']) }}" alt="" class="{{ $c['trophySize'] }} animate__animated animate__bounceIn" @if($c['delay']) style="animation-delay: {{ $c['delay'] }};" @endif>
                            <div class="w-full {{ $c['height'] }} {{ $c['bg'] }} rounded-t-xl flex items-end justify-center pb-3">
                                <span class="text-base {{ $c['levelText'] }}">{{ 'เลเวล' }} {{ $student->level }}</span>
                            </div>
                        </a>
                    @endforeach
                    </div>
                </div>

                {{-- Rank 4+ --}}
                @if($topStudents->count() > 3)
                    <div class="mt-8 -mx-6 lg:-mx-8 border-t border-[#dedee5]">
                        @foreach($topStudents->skip(3)->take($limit)->values() as $i => $record)
                            @php $rank = $i + 4;
                            $isMe = auth()->id() === $record->user_id; @endphp
                            <a href="{{ route('profile', $record->user) }}" wire:navigate
                                class="flex items-center gap-3 px-6 lg:px-8 py-3 {{ !$loop->last || $topStudents->count() > 3 + $limit ? 'border-b border-[#dedee5]' : '' }} {{ $isMe ? 'bg-[var(--ll-blue-subtle)]' : 'hover:bg-(--ll-blue-faint)' }} transition-colors">
                                <span class="text-sm font-bold text-(--ll-blue) w-6 text-center shrink-0">{{ $rank }}</span>
                                <div class="relative inline-block shrink-0">
                                    <img src="{{ $record->user->avatar_url }}"
                                        class="w-9 h-9 rounded-full object-cover border border-[#dedee5] bg-white">
                                    @if($record->user->active_avatar_frame && !str_starts_with($record->user->active_avatar_frame, 'border'))
                                        <img src="{{ asset($record->user->active_avatar_frame) }}"
                                             class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                    @elseif($record->user->active_avatar_frame)
                                        <div class="absolute inset-0 rounded-full {{ $record->user->active_avatar_frame }} pointer-events-none"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#101114] truncate {{ $record->user->active_name_color ?? '' }}">
                                        {{ $record->user->name }}
                                        @foreach($record->user->displayed_badges as $badge)
                                            <img src="{{ asset($badge->badge_image ?: 'images/achievements/Achievements_Novice.png') }}"
                                                alt="{{ $badge->name }}" title="{{ $badge->name }}" class="inline h-4 w-4 shrink-0 object-contain align-text-bottom">
                                        @endforeach
                                        @if($isMe)
                                            <span
                                                class="ml-1 text-[10px] font-bold text-(--ll-blue) bg-[var(--ll-blue-subtle)] px-1.5 py-0.5 rounded-[4px]">YOU</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-[#9497a9]">{{ 'เลเวล' }} {{ $record->level }}</p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="text-sm font-bold text-[#686b82]">{{ number_format($record->xp) }}</span>
                                    <x-icon name="bolt" class="text-(--ll-blue) h-4 w-4 shrink-0" />
                                </div>
                            </a>
                        @endforeach

                        @if($topStudents->count() > 3 + $limit)
                            <div class="px-4 py-3 text-center">
                                <button wire:click="loadMore" wire:loading.attr="disabled"
                                    class="py-2.5 px-8 bg-white border border-(--ll-blue-dark) text-(--ll-blue-dark) hover:bg-(--ll-blue-hover) font-medium rounded-lg text-sm transition-colors cursor-pointer">
                                    <span wire:loading.remove>{{ 'ดูเพิ่มอีก 10 อันดับ' }}</span>
                                    <span wire:loading class="inline-flex items-center gap-2">
                                        <x-icon name="spinner" class="h-4 w-4 animate-spin" /> {{ 'กำลังโหลด...' }}
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        @endif
    </div>
</div>
