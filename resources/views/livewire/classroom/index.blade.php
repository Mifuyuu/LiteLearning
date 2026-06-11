@section('page-title', auth()->user()->isTeacher() ? __('ชั้นเรียนของฉัน') : __('Classrooms'))
<div data-empty-state-layout="{{ $classrooms->isEmpty() ? 'remaining-content' : 'classroom-grid' }}"
    class="flex min-h-[calc(100dvh-6.75rem)] flex-col animate__animated animate__fadeIn lg:min-h-[calc(100dvh-3rem)]">

    {{-- ── Search / filter bar ── --}}
    <div class="bg-white border border-[#dedee5] rounded-2xl shadow-[rgba(0,0,0,0.03)_0px_4px_24px] px-4 py-3">
        <div class="flex items-center gap-3 flex-wrap">

            {{-- Search --}}
            <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-50 border border-[#dedee5]">
                <i class="fas fa-search text-[#9497a9] text-xs"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="ค้นหาห้องเรียน..."
                    class="bg-transparent border-none outline-none text-[#101114] placeholder-[#9497a9] text-base w-44" />
            </div>

            @if(auth()->user()->isStudent())
                @livewire('classroom.join-classroom')
            @endif

            @if(auth()->user()->isTeacher())
                {{-- Archived checkbox for teacher --}}
                <label class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm cursor-pointer transition-all"
                    :class="$wire.showArchived ? 'bg-[rgba(133,91,251,0.16)] text-[#7132f5]' : 'text-[#686b82] hover:text-[#101114]'">
                    <input type="checkbox" wire:model.live="showArchived" class="rounded border-[#dedee5] text-[#7132f5] focus:ring-[#7132f5]">
                    {{ __('แสดงเก็บถาวร') }}
                </label>
            @endif

            {{-- Count --}}
            <span class="ml-auto text-sm text-[#9497a9]">{{ $classrooms->count() }} ห้องเรียน</span>
        </div>
    </div>

    {{-- ── Empty state ── --}}
    @if($classrooms->isEmpty())
        <div data-classroom-empty-state data-empty-state-centered="true"
            class="flex min-h-0 flex-1 items-center justify-center">
            <div class="flex flex-col items-center gap-5 text-center">
                <div data-empty-state-image-crop
                    class="relative h-44 w-52 overflow-hidden sm:h-52 sm:w-64">
                    <img src="{{ asset('images/empty.webp') }}" alt=""
                        class="absolute left-0 top-0 h-auto w-[25rem] max-w-none -translate-x-[5.2rem] -translate-y-[3.5rem] select-none sm:w-[30rem] sm:-translate-x-[6.25rem] sm:-translate-y-[4.25rem]" />
                </div>
                <p class="text-base font-medium text-[#686b82]">ยังไม่มีดวงดาวที่ค้นพบ...</p>
            </div>
        </div>

    @else
        {{-- ── Classroom grid ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-6 items-stretch mt-6">
            @foreach($classrooms as $index => $classroom)
                @php
                    $tc = $classroom->themeCategory;
                    $planetNum = $tc
                        ? str_pad($tc->planet_number, 2, '0', STR_PAD_LEFT)
                        : str_pad(($index % 21) + 1, 2, '0', STR_PAD_LEFT);
                    $color = $tc?->color ?? '#7132f5';
                @endphp

                {{-- ── Card wrapper — extra top padding so planet has room to overflow ── --}}
                <div class="relative pt-20 group h-full">

                    {{-- ── Planet overflows card from top ── --}}
                    <div class="absolute top-7 left-1/2 -translate-x-1/2 z-10 transition-transform duration-300 group-hover:-translate-y-2"
                        style="filter: drop-shadow(0 8px 24px {{ $color }}66);">
                        <img src="/images/planets/planet_{{ $planetNum }}.svg" alt="{{ $classroom->name }}"
                            class="w-32 h-32 select-none" />
                    </div>

                    {{-- ── White card ── --}}
                    <a wire:navigate href="{{ route('classroom.show', $classroom) }}"
                        class="rounded-2xl overflow-hidden border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px] group-hover:border-[rgba(113,50,245,0.3)] flex flex-col h-full bg-white">

                        {{-- Card body ── --}}
                        <div class="px-5 pt-16 pb-5 flex flex-col flex-1">

                            {{-- Name + badges --}}
                            <div class="mb-3">
                                <h3 class="text-base font-bold text-[#101114] leading-snug truncate">{{ $classroom->name }}</h3>
                                @if($classroom->section)
                                    <p class="text-xs text-[#9497a9] mt-0.5 truncate">{{ $classroom->section }}</p>
                                @endif
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @if($classroom->is_archived)
                                        <span
                                            class="inline-flex items-center text-xs px-2 py-0.5 rounded-[6px] font-medium bg-[rgba(104,107,130,0.12)] text-[#484b5e]">
                                            เก็บถาวร
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="h-px bg-[#dedee5] mb-3"></div>

                            {{-- Teacher --}}
                            <div class="flex items-center gap-2 mb-3">
                                <img src="{{ $classroom->teacher->avatar_url }}" alt="{{ $classroom->teacher->name }}"
                                    class="w-7 h-7 rounded-full object-cover shrink-0 ring-2 ring-white" />
                                <span class="text-xs text-[#686b82] truncate">{{ $classroom->teacher->name }}</span>
                            </div>


                            {{-- Stats --}}
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <div class="rounded-xl px-3 py-2 text-center border border-[#dedee5] bg-[rgba(133,91,251,0.04)]">
                                    <div class="text-base font-bold text-[#101114]">{{ $classroom->students()->count() }}</div>
                                    <div class="text-[10px] text-[#9497a9] mt-0.5">นักสำรวจ</div>
                                </div>
                                <div class="rounded-xl px-3 py-2 text-center border border-[#dedee5] bg-[rgba(133,91,251,0.04)]">
                                    <div class="text-base font-bold text-[#101114]">
                                        {{ $classroom->assignments()->published()->count() }}
                                    </div>
                                    <div class="text-[10px] text-[#9497a9] mt-0.5">ทรัพยากร</div>
                                </div>
                            </div>

                            {{-- Description --}}
                            @if($classroom->description)
                                <p class="text-xs text-[#9497a9] leading-relaxed line-clamp-2 mt-3">{{ $classroom->description }}</p>
                            @endif

                            {{-- CTA --}}
                            <div class="mt-auto pt-4">
                                <div
                                    class="bg-[#7132f5] text-white flex items-center justify-center gap-2 w-full py-2.5 text-center rounded-[12px] text-sm font-semibold select-none hover:bg-[#5741d8] transition-colors">
                                    <x-icon name="rocket-launch" class="h-4 w-4" />
                                    สำรวจดวงดาว
                                </div>
                            </div>

                        </div>
                    </a>

                </div>
            @endforeach
        </div>
    @endif

</div>
