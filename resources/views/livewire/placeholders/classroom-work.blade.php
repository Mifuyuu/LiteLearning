@section('page-title', (isset($classroom) ? ('งานในชั้นเรียน' . ' - ' . $classroom->name) : 'งานในชั้นเรียน'))
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">
            {{ \Illuminate\Support\Str::limit($classroom->name, 15, '..') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'งานในชั้นเรียน' }}</span>
    </nav>
@endsection
@endif
@php
    $isManager = isset($classroom) ? $classroom->canManageClassroom(auth()->user()) : auth()->user()->isTeacher();
@endphp
<div class="max-w-4xl mx-auto">
    <section class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)]">

        {{-- Scope tabs & Create button --}}
        <div class="p-4 sm:p-6 border-b border-[#dedee5] flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="skeleton h-9 w-16 rounded-[10px]"></div>
                <div class="skeleton h-9 w-20 rounded-[10px]"></div>
                <div class="skeleton h-9 w-20 rounded-[10px]"></div>
            </div>
            @if($isManager)
                <div class="skeleton ml-auto h-10 w-24 rounded-[10px] shrink-0"></div>
            @endif
        </div>
        
        {{-- Work content mockup --}}
        <div class="p-6 space-y-6">
            @for($t = 0; $t < 2; $t++)
                <section class="space-y-5">
                    <section class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="skeleton h-3.5 w-32"></div>
                            <div class="h-px flex-1 bg-[#dedee5]"></div>
                        </div>
                        <div class="space-y-3">
                            @for($i = 0; $i < 2; $i++)
                                <div class="rounded-2xl border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
                                            <div class="skeleton h-10 w-10 shrink-0 rounded-[10px]"></div>
                                            <div class="min-w-0 space-y-2">
                                                <div class="skeleton h-5 w-1/3"></div>
                                                <div class="skeleton h-3 w-1/4"></div>
                                            </div>
                                        </div>
                                        <div class="skeleton h-6 w-20 shrink-0 rounded-md"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </section>
                </section>
            @endfor
        </div>
    </section>
</div>
