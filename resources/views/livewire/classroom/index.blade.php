@section('page-title', auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน')
<div data-empty-state-layout="{{ $classrooms->isEmpty() ? 'remaining-content' : 'classroom-grid' }}"
    class="max-w-4xl mx-auto">

    {{-- ── Search / filter bar ── --}}
    <div class="bg-white border-3 border-[#dedee5] rounded-xl shadow-[rgba(0,0,0,0.03)_0px_4px_24px] px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Search --}}
            <div class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gray-50 border border-[#dedee5] flex-1 sm:flex-none">
                <x-icon name="magnifying-glass" class="text-[#9497a9] h-4 w-4 shrink-0" />
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="ค้นหาดาวเคราะห์..."
                    class="bg-transparent border-none outline-none text-[#101114] placeholder-[#9497a9] text-sm w-full sm:w-44" />
            </div>

            @if(auth()->user()->isStudent())
                @livewire('classroom.join-classroom')
            @endif

            @if(auth()->user()->isTeacher())
                {{-- Archived checkbox + count: share one row on mobile --}}
                <div class="flex items-center justify-between w-full sm:contents">
                    <label class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm cursor-pointer text-[#686b82]">
                        <input type="checkbox" wire:model.live="showArchived" class="checkbox checkbox-sm">
                        {{ 'แสดงเก็บถาวร' }}
                    </label>

                    {{-- Count (mobile only) --}}
                    <span class="sm:hidden text-sm text-[#9497a9]">ค้นพบดาวเคราะห์ {{ $classrooms->count() }} ดวง</span>
                </div>
            @endif

            {{-- Count (sm+) --}}
            <span class="hidden sm:block ml-auto text-sm text-[#9497a9]">ค้นพบดาวเคราะห์ {{ $classrooms->count() }} ดวง</span>
        </div>
    </div>

    {{-- ── Empty state ── --}}
    @if($classrooms->isEmpty())
        <div data-classroom-empty-state data-empty-state-centered="true"
            class="mt-4 bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] flex min-h-[calc(100vh-8.5rem)] flex-col items-center justify-center p-8 text-center">
            <div data-empty-state-image-crop>
                <img src="{{ asset('images/empty.svg') }}" alt=""
                    class="h-44 w-44 select-none object-contain" />
            </div>
            <p class="mt-4 text-base font-medium text-[#686b82]">ยังไม่มีดวงดาวที่ค้นพบ...</p>
        </div>

    @else
        {{-- ── Classroom grid ── --}}
        <div class="space-y-4 mt-4">
            @foreach($classrooms as $index => $classroom)
                @php
                    $tc = $classroom->themeCategory;
                    $planetKey = $tc?->planet_key ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['planet_key'];
                @endphp

                <a wire:navigate href="{{ route('classroom.show', $classroom) }}"
                    x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ $index * 60 }})" x-show="show" x-cloak
                    x-transition:enter="transition ease-out duration-400"
                    x-transition:enter-start="opacity-0 translate-y-6"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="classroom-card flex items-center gap-5 rounded-xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5 ring-2 ring-transparent transition-all duration-300 hover:border-[#3293F5]">

                    {{-- Planet --}}
                    <div class="shrink-0">
                        <img src="/images/planets/planet_{{ $planetKey }}.svg" alt="{{ $classroom->name }}"
                            class="w-24 h-24 select-none" />
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-[#101114] truncate">
                                    {{ $classroom->name }}
                                    @if($classroom->section)
                                        <span class="text-[#9497a9] font-medium">({{ $classroom->section }})</span>
                                    @endif
                                </h3>
                            </div>
                            @if($classroom->is_archived)
                                <span class="shrink-0 inline-flex items-center text-xs px-2 py-0.5 rounded-md font-medium bg-[rgba(104,107,130,0.12)] text-[#484b5e]">เก็บถาวร</span>
                            @endif
                        </div>

                        @if($classroom->description)
                            <p class="text-xs text-[#9497a9] leading-relaxed line-clamp-1 mt-1.5">{{ $classroom->description }}</p>
                        @endif

                        <div class="flex items-center gap-3 mt-2 text-xs text-[#686b82]">
                            <span class="flex items-center gap-1">
                                <img src="{{ $classroom->teacher->avatar_url }}" alt="{{ $classroom->teacher->name }}"
                                    class="w-5 h-5 rounded-full object-cover shrink-0" />
                                <span class="hidden sm:inline truncate max-w-30">{{ $classroom->teacher->name }}</span>
                            </span>
                            <span class="text-[#dedee5]">|</span>
                            <span class="flex items-center gap-1">
                                <x-icon name="user" class="h-3.5 w-3.5 shrink-0 sm:hidden" />
                                <span>{{ $classroom->students()->count() }}</span>
                                <span class="hidden sm:inline">นักสำรวจ</span>
                            </span>
                            <span class="text-[#dedee5]">|</span>
                            <span class="flex items-center gap-1">
                                <x-icon name="backpack" class="h-3.5 w-3.5 shrink-0 sm:hidden" />
                                <span>{{ $classroom->assignments()->published()->count() }}</span>
                                <span class="hidden sm:inline">ทรัพยากร</span>
                        </div>
                    </div>

                    {{-- Arrow --}}
                    <div class="shrink-0 text-[#9497a9] group-hover:text-(--ll-blue) transition-colors">
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
