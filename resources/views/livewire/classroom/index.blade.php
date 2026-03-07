@section('page-title', __('Classrooms'))
<div style="zoom: {{ auth()->user()->ui_scale }}%;"
    class="px-6 py-6 max-w-7xl mx-auto animate__animated animate__fadeIn">

    {{-- ── Search / filter bar ── --}}
    <div class="bg-white/6 border border-white/10 rounded-2xl px-4 py-3">
        <div class="flex items-center gap-3 flex-wrap">

            {{-- Search --}}
            <div
                class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md">
                <i class="fas fa-search text-white/40 text-xs"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="ค้นหาห้องเรียน..."
                    class="bg-transparent border-none outline-none text-white placeholder-white/40 text-base w-44" />
            </div>

            {{-- Filter pills --}}
            <div class="flex items-center gap-1.5">
                <button wire:click="$set('filter','all')"
                    class="px-4 py-2 rounded-2xl text-sm font-semibold transition-all {{ $filter === 'all' ? 'bg-white/20 text-white shadow' : 'text-white/50 hover:text-white hover:bg-white/10' }}">
                    ทั้งหมด
                </button>
                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    <button wire:click="$set('filter','teaching')"
                        class="px-4 py-2 rounded-2xl text-sm font-semibold transition-all {{ $filter === 'teaching' ? 'bg-white/20 text-white shadow' : 'text-white/50 hover:text-white hover:bg-white/10' }}">
                        สอน
                    </button>
                @endif
                <button wire:click="$set('filter','enrolled')"
                    class="px-4 py-2 rounded-2xl text-sm font-semibold transition-all {{ $filter === 'enrolled' ? 'bg-white/20 text-white shadow' : 'text-white/50 hover:text-white hover:bg-white/10' }}">
                    ลงทะเบียน
                </button>
                <button wire:click="$set('filter','archived')"
                    class="px-4 py-2 rounded-2xl text-sm font-semibold transition-all {{ $filter === 'archived' ? 'bg-white/20 text-white shadow' : 'text-white/50 hover:text-white hover:bg-white/10' }}">
                    เก็บถาวร
                </button>
            </div>

            {{-- Count --}}
            <span class="ml-auto text-sm text-white/30">{{ $classrooms->count() }} ห้องเรียน</span>
        </div>
    </div>

    {{-- ── Empty state ── --}}
    @if($classrooms->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 gap-5 text-center">
            <img src="/images/planets/planet_01.svg" alt="planet" class="w-28 h-28 opacity-30" />
            <p class="text-white/30 text-sm">ยังไม่มีห้องเรียน</p>
        </div>

    @else
        {{-- ── Classroom grid ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-6 items-stretch">
            @foreach($classrooms as $index => $classroom)
                @php
                    $tc = $classroom->themeCategory;
                    $planetNum = $tc
                        ? str_pad($tc->planet_number, 2, '0', STR_PAD_LEFT)
                        : str_pad(($index % 21) + 1, 2, '0', STR_PAD_LEFT);
                    $color = $tc ? $tc->preview_color : ($classroom->theme_color ?? '#8B5CF6');
                @endphp

                {{-- ── Card wrapper — extra top padding so planet has room to overflow ── --}}
                <div class="relative pt-20 group h-full">

                    {{-- ── Planet overflows card from top ── --}}
                    <div class="absolute top-7 left-1/2 -translate-x-1/2 z-10 transition-transform duration-300 group-hover:-translate-y-2"
                        style="filter: drop-shadow(0 8px 24px {{ $color }}66);">
                        <img src="/images/planets/planet_{{ $planetNum }}.svg" alt="{{ $classroom->name }}"
                            class="w-32 h-32 select-none" />
                    </div>

                    {{-- ── White 3D card ── --}}
                    <a wire:navigate href="{{ route('classroom.show', $classroom) }}"
                        class="rounded-2xl overflow-hidden transition-transform duration-300 group-hover:-translate-y-1 flex flex-col h-full"
                        style="background: #ffffff; box-shadow: 0 6px 0 0 #c4b5fd; border: 3px solid #c4b5fd;">

                        {{-- Card body ── --}}
                        <div class="px-5 pt-16 pb-5 flex flex-col flex-1">

                            {{-- Name + badges --}}
                            <div class="mb-3">
                                <h3 class="text-base font-bold text-gray-900 leading-snug truncate">{{ $classroom->name }}</h3>
                                @if($classroom->section)
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $classroom->section }}</p>
                                @endif
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @if($classroom->isOwnedBy(auth()->user()))
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                                            style="background: #fef9c3; color: #854d0e;">
                                            <i class="fas fa-crown text-[10px]"></i> ผู้สอน
                                        </span>
                                    @endif
                                    @if($classroom->is_archived)
                                        <span
                                            class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-500">
                                            เก็บถาวร
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="h-px bg-gray-100 mb-3"></div>

                            {{-- Teacher --}}
                            <div class="flex items-center gap-2 mb-3">
                                <img src="{{ $classroom->teacher->avatar_url }}" alt="{{ $classroom->teacher->name }}"
                                    class="w-7 h-7 rounded-full object-cover shrink-0 ring-2 ring-white" />
                                <span class="text-xs text-gray-500 truncate">{{ $classroom->teacher->name }}</span>
                            </div>

                            {{-- Subject --}}
                            @if($classroom->subject)
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                                        style="background: {{ $color }}18;">
                                        <i class="fas fa-book-open text-[10px]" style="color: {{ $color }};"></i>
                                    </div>
                                    <span class="text-xs text-gray-500 truncate">{{ $classroom->subject }}</span>
                                </div>
                            @endif

                            {{-- Stats --}}
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <div class="rounded-xl px-3 py-2 text-center"
                                    style="background: #f8f8f8; border: 1px solid #ececec;">
                                    <div class="text-base font-bold text-gray-800">{{ $classroom->students()->count() }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">นักสำรวจ</div>
                                </div>
                                <div class="rounded-xl px-3 py-2 text-center"
                                    style="background: #f8f8f8; border: 1px solid #ececec;">
                                    <div class="text-base font-bold text-gray-800">
                                        {{ $classroom->assignments()->published()->count() }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">ทรัพยากร</div>
                                </div>
                            </div>

                            {{-- Description --}}
                            @if($classroom->description)
                                <p class="text-xs text-gray-400 leading-relaxed line-clamp-2 mt-3">{{ $classroom->description }}</p>
                            @endif

                            {{-- CTA --}}
                            <div class="mt-auto pt-4">
                                <div
                                    class="btn-3d btn-3d--indigo block w-full py-2.5 text-center rounded-lg text-sm select-none">
                                    <i class="fas fa-rocket"></i>
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