@section('page-title', (isset($classroom) ? ('กระดานสนทนา' . ' - ' . $classroom->name) : 'กระดานสนทนา'))
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            {{ $classroom->name }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'กระดานสนทนา' }}</span>
    </nav>
@endsection
@endif
@php
    $isManager = isset($classroom) ? $classroom->canManageClassroom(auth()->user()) : auth()->user()->isTeacher();
@endphp
<div class="space-y-5 max-w-4xl mx-auto">

    <section class="rounded-xl border-3 border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)]">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="skeleton h-3 w-20"></div>
                <div class="skeleton mt-1 h-8 w-36"></div>
            </div>
            @if($isManager)
                <div class="skeleton h-10 w-28 rounded-[10px]"></div>
            @endif
        </div>

        <div class="mt-5 space-y-3">
            @for($i = 0; $i < 3; $i++)
                <div class="rounded-xl border border-[#dedee5] bg-[rgba(37,99,235,0.02)] p-4">
                    <div class="flex items-start gap-3">
                        <div class="skeleton h-10 w-10 shrink-0 rounded-[10px]"></div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="skeleton h-4 w-32"></div>
                            <div class="skeleton h-3 w-20"></div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-2">
                        <div class="skeleton h-4 w-full"></div>
                        <div class="skeleton h-4 w-5/6"></div>
                        <div class="skeleton h-4 w-2/3"></div>
                    </div>
                    {{-- Comment bar --}}
                    <div class="mt-3 flex items-center gap-2">
                        <div class="skeleton h-8 w-8 shrink-0 rounded-full"></div>
                        <div class="skeleton h-9 flex-1 rounded-[10px]"></div>
                    </div>
                </div>
            @endfor
        </div>
    </section>
</div>
