@section('page-title', 'กระดานผู้นำ')

<div class="max-w-4xl mx-auto space-y-4 ">

    @if($topStudents->isNotEmpty())

        {{-- Podium: Top 3 --}}
        <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] px-6 pt-8 pb-0 overflow-hidden">

            {{-- Podium layout: 2nd | 1st | 3rd --}}
            <div class="flex items-end justify-center gap-3">

                {{-- 2nd Place --}}
                @if($topStudents->has(1))
                    <a href="{{ route('profile', $topStudents[1]->user) }}" wire:navigate class="flex flex-col items-center w-1/3 max-w-[160px]">
                        <div class="relative inline-block mb-2">
                            <img src="{{ $topStudents[1]->user->avatar_url }}"
                                class="w-14 h-14 rounded-full object-cover ring-2 ring-[rgba(37,99,235,0.4)] shadow bg-white">
                            @if($topStudents[1]->user->active_avatar_frame && !str_starts_with($topStudents[1]->user->active_avatar_frame, 'border'))
                                <img src="{{ asset($topStudents[1]->user->active_avatar_frame) }}" 
                                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                            @elseif($topStudents[1]->user->active_avatar_frame)
                                <div class="absolute inset-0 rounded-full {{ $topStudents[1]->user->active_avatar_frame }} pointer-events-none"></div>
                            @endif
                        </div>
                        <p
                            class="text-sm font-semibold text-[#686b82] truncate w-full text-center mb-1 {{ $topStudents[1]->user->active_name_color ?? '' }}">
                            {{ $topStudents[1]->user->name }}</p>
                        <div class="flex items-center gap-1 text-xs text-[#9497a9] mb-3">
                            <span class="font-bold text-[#686b82]">{{ number_format($topStudents[1]->xp) }}</span>
                            <x-icon name="bolt" class="text-[var(--ll-blue)] h-4 w-4 shrink-0" />
                        </div>
                        {{-- Podium block --}}
                        <div class="w-full h-[100px] bg-[rgba(37,99,235,0.24)] rounded-t-xl flex flex-col items-center justify-center gap-1 shadow-inner animate__animated animate__slideInUp" style="animation-delay: 0.2s;">
                            <span class="text-2xl font-black text-[var(--ll-blue)]">2</span>
                            <span class="text-base text-[var(--ll-blue)]/70">{{ 'เลเวล' }} {{ $topStudents[1]->level }}</span>
                        </div>
                    </a>
                @endif

                {{-- 1st Place --}}
                @if($topStudents->has(0))
                    <a href="{{ route('profile', $topStudents[0]->user) }}" wire:navigate class="flex flex-col items-center w-1/3 max-w-[180px]">
                        <i class="fas fa-crown text-lg text-amber-400 mb-1 animate__animated animate__bounceIn animate__delay-1s"></i>
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
                            class="text-sm sm:text-base font-bold text-[#101114] truncate w-full text-center mb-1 {{ $topStudents[0]->user->active_name_color ?? '' }}">
                            {{ $topStudents[0]->user->name }}</p>
                        <div class="flex items-center gap-1 text-xs text-[#9497a9] mb-3">
                            <span class="font-bold text-[#686b82]">{{ number_format($topStudents[0]->xp) }}</span>
                            <x-icon name="bolt" class="text-[var(--ll-blue)] h-4 w-4 shrink-0" />
                        </div>
                        {{-- Podium block --}}
                        <div
                            class="w-full h-[145px] bg-[var(--ll-blue)] rounded-t-xl flex flex-col items-center justify-center gap-1 shadow-inner animate__animated animate__slideInUp" style="animation-delay: 0.4s;">
                            <span class="text-3xl font-black text-white">1</span>
                            <span class="text-base text-white/70">{{ 'เลเวล' }} {{ $topStudents[0]->level }}</span>
                        </div>
                    </a>
                @endif

                {{-- 3rd Place --}}
                @if($topStudents->has(2))
                    <a href="{{ route('profile', $topStudents[2]->user) }}" wire:navigate class="flex flex-col items-center w-1/3 max-w-[160px]">
                        <div class="relative inline-block mb-2">
                            <img src="{{ $topStudents[2]->user->avatar_url }}"
                                class="w-14 h-14 rounded-full object-cover ring-2 ring-[rgba(37,99,235,0.2)] shadow bg-white">
                            @if($topStudents[2]->user->active_avatar_frame && !str_starts_with($topStudents[2]->user->active_avatar_frame, 'border'))
                                <img src="{{ asset($topStudents[2]->user->active_avatar_frame) }}" 
                                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                            @elseif($topStudents[2]->user->active_avatar_frame)
                                <div class="absolute inset-0 rounded-full {{ $topStudents[2]->user->active_avatar_frame }} pointer-events-none"></div>
                            @endif
                        </div>
                        <p
                            class="text-sm font-semibold text-[#686b82] truncate w-full text-center mb-1 {{ $topStudents[2]->user->active_name_color ?? '' }}">
                            {{ $topStudents[2]->user->name }}</p>
                        <div class="flex items-center gap-1 text-xs text-[#9497a9] mb-3">
                            <span class="font-bold text-[#686b82]">{{ number_format($topStudents[2]->xp) }}</span>
                            <x-icon name="bolt" class="text-[var(--ll-blue)] h-4 w-4 shrink-0" />
                        </div>
                        {{-- Podium block --}}
                        <div class="w-full h-[72px] bg-[rgba(37,99,235,0.12)] rounded-t-xl flex flex-col items-center justify-center gap-1 animate__animated animate__slideInUp" style="animation-delay: 0s;">
                            <span class="text-2xl font-black text-[var(--ll-blue)]">3</span>
                            <span class="text-base text-[var(--ll-blue)]/70">{{ 'เลเวล' }} {{ $topStudents[2]->level }}</span>
                        </div>
                    </a>
                @endif

            </div>
        </div>

        {{-- Rank 4+ --}}
        @if($topStudents->count() > 3)
            <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">
                @foreach($topStudents->skip(3)->take($limit)->values() as $i => $record)
                    @php $rank = $i + 4;
                    $isMe = auth()->id() === $record->user_id; @endphp
                    <a href="{{ route('profile', $record->user) }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 {{ !$loop->last || $topStudents->count() > 3 + $limit ? 'border-b border-[#dedee5]' : '' }} {{ $isMe ? 'bg-[var(--ll-blue-subtle)]' : 'hover:bg-[var(--ll-blue-faint)]' }} transition-colors">
                        <span class="text-sm font-bold text-[var(--ll-blue)] w-6 text-center shrink-0">{{ $rank }}</span>
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
                                @if($isMe)
                                    <span
                                        class="ml-1 text-[10px] font-bold text-[var(--ll-blue)] bg-[var(--ll-blue-subtle)] px-1.5 py-0.5 rounded-[4px]">YOU</span>
                                @endif
                            </p>
                            <p class="text-xs text-[#9497a9]">{{ 'เลเวล' }} {{ $record->level }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <span class="text-sm font-bold text-[#686b82]">{{ number_format($record->xp) }}</span>
                            <x-icon name="bolt" class="text-[var(--ll-blue)] h-4 w-4 shrink-0" />
                        </div>
                    </a>
                @endforeach

                @if($topStudents->count() > 3 + $limit)
                    <div class="px-4 py-3 text-center">
                        <button wire:click="loadMore" wire:loading.attr="disabled"
                            class="py-2.5 px-8 bg-white border border-[var(--ll-blue-dark)] text-[var(--ll-blue-dark)] hover:bg-[var(--ll-blue-hover)] font-medium rounded-[8px] text-sm transition-colors cursor-pointer">
                            <span wire:loading.remove>{{ 'ดูเพิ่มอีก 10 อันดับ' }}</span>
                            <span wire:loading class="inline-flex items-center gap-2">
                                <x-icon name="spinner" class="h-4 w-4 animate-spin" /> {{ 'กำลังโหลด...' }}
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

    @else
        <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-16 text-center">
            <x-icon name="trophy" class="h-9 w-9 text-[#dedee5] mb-3 block" />
            <p class="text-[#9497a9] text-sm">{{ 'ยังไม่มีข้อมูลการจัดอันดับ' }}</p>
        </div>
    @endif

</div>