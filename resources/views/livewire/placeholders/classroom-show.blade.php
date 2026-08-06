@section('page-title', isset($classroom) ? $classroom->name : 'ห้องเรียน')
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ Illuminate\Support\Str::limit($classroom->name, 30) }}</span>
    </nav>
@endsection
@endif
<div class="space-y-5">
    @if(isset($classroom))
        @include('livewire.classroom.partials.subnav', ['classroom' => $classroom])
    @endif

    {{-- Classroom Cover Card --}}
    <div class="overflow-hidden rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="h-2 w-full" style="background-color: {{ isset($classroom) ? ($classroom->themeCategory?->color ?? '#2563eb') : '#2563eb' }};"></div>
        <div class="p-5 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="skeleton h-3 w-16"></div>
                    <div class="skeleton h-8 w-64"></div>
                    <div class="skeleton h-4 w-5/6"></div>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <div class="skeleton h-7 w-28 rounded-[8px]"></div>
                        <div class="skeleton h-7 w-20 rounded-[8px]"></div>
                        <div class="skeleton h-7 w-24 rounded-[8px]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Three Cards Grid --}}
    <div class="grid gap-5 lg:grid-cols-3">
        @for($i = 0; $i < 3; $i++)
            <div class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="skeleton h-10 w-10 rounded-[10px]"></div>
                    <div class="skeleton h-8 w-8"></div>
                </div>
                <div class="skeleton h-5 w-28"></div>
                <div class="skeleton h-4 w-full"></div>
            </div>
        @endfor
    </div>

    {{-- Bottom Layout --}}
    <div class="grid gap-5 {{ auth()->user()->isTeacher() ? 'xl:grid-cols-[minmax(0,1fr)_340px]' : 'grid-cols-1' }}">
        {{-- Left: Needs Attention --}}
        <div class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] space-y-4">
            <div class="space-y-1">
                <div class="skeleton h-3 w-20"></div>
                <div class="skeleton h-5 w-44"></div>
            </div>
            <div class="space-y-3">
                @for($i = 0; $i < 3; $i++)
                    <div class="rounded-[10px] border border-[#dedee5] px-3 py-3 space-y-2">
                        <div class="skeleton h-4 w-3/4"></div>
                        <div class="skeleton h-3.5 w-1/3"></div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Right: Join Code (Only for Teachers) --}}
        @if(auth()->user()->isTeacher())
            <div class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] space-y-4">
                <div class="space-y-1">
                    <div class="skeleton h-3 w-20"></div>
                    <div class="skeleton h-5 w-28"></div>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-[10px] border border-[#dedee5] bg-[var(--ll-blue-faint)] px-4 py-3">
                    <div class="skeleton h-8 w-28"></div>
                    <div class="skeleton h-9 w-9 rounded-[9px]"></div>
                </div>
            </div>
        @endif
    </div>
</div>
