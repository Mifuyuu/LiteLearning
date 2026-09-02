@section('page-title', (isset($classroom) ? ('สมาชิก' . ' - ' . $classroom->name) : 'สมาชิก'))
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            ...
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">
            {{ \Illuminate\Support\Str::limit($classroom->name, 10, '..') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'สมาชิก' }}</span>
    </nav>
@endsection
@endif
@php
    $isManager = isset($classroom) ? $classroom->canManageClassroom(auth()->user()) : auth()->user()->isTeacher();
    $isOwnerOrAdmin = isset($classroom)
        ? ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
        : auth()->user()->isTeacher();
@endphp
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Single card: sort bar + teachers block + divider + students block --}}
    <section class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)]">

        {{-- Sort --}}
        <div class="flex flex-wrap items-center gap-2 p-4">
            <div class="skeleton h-9 w-24 rounded-[10px]"></div>
            <div class="skeleton h-9 w-24 rounded-[10px]"></div>
        </div>

        <div class="border-t border-[#dedee5] mx-6"></div>

        {{-- Teachers --}}
        <div class="p-6 pb-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="skeleton h-3.5 w-24"></div>
                    <div class="skeleton mt-2 h-6 w-48"></div>
                </div>
                <div class="skeleton h-6 w-8 rounded-lg"></div>
            </div>

            <div class="mt-6 space-y-3">
                {{-- Owner row --}}
                <div class="rounded-xl border border-[#dedee5] bg-(--ll-blue-faint) p-4">
                    <div class="flex items-center gap-3">
                        <div class="skeleton h-11 w-11 shrink-0 rounded-2xl"></div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="skeleton h-4 w-1/3"></div>
                            <div class="skeleton h-3 w-1/2"></div>
                        </div>
                        <div class="skeleton h-5 w-16 shrink-0 rounded-md"></div>
                    </div>
                </div>

                {{-- Co-teacher row --}}
                <div class="rounded-xl border border-[#dedee5] bg-white p-4">
                    <div class="flex items-center gap-3">
                        <div class="skeleton h-11 w-11 shrink-0 rounded-2xl"></div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="skeleton h-4 w-1/3"></div>
                            <div class="skeleton h-3 w-1/2"></div>
                        </div>
                        <div class="skeleton h-5 w-20 shrink-0 rounded-lg"></div>
                        @if($isOwnerOrAdmin)
                            <div class="skeleton ml-auto h-9 w-9 shrink-0 rounded-[10px]"></div>
                        @endif
                    </div>
                </div>
            </div>

            @if($isOwnerOrAdmin)
                {{-- Add co-teacher form --}}
                <div class="mt-6 rounded-xl border border-dashed border-[#dedee5] bg-(--ll-blue-faint) p-4">
                    <div class="skeleton h-4 w-28"></div>
                    <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                        <div class="skeleton h-11 flex-1 rounded-xl"></div>
                        <div class="skeleton h-11 w-24 rounded-xl"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Divider --}}
        <div class="border-t border-[#dedee5] mx-6"></div>

        {{-- Students --}}
        <div class="p-6 pt-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="skeleton h-3.5 w-24"></div>
                    <div class="skeleton mt-2 h-6 w-32"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="skeleton h-6 w-8 rounded-lg"></div>
                    @if($isOwnerOrAdmin)
                        <div class="skeleton h-10 w-10 rounded-[10px]"></div>
                    @endif
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @for($i = 0; $i < 4; $i++)
                    <div class="rounded-xl border border-[#dedee5] bg-(--ll-blue-faint) p-4">
                        <div class="flex items-center gap-3">
                            <div class="skeleton h-11 w-11 shrink-0 rounded-2xl"></div>
                            <div class="min-w-0 flex-1">
                                <div class="skeleton h-4 w-1/3"></div>
                            </div>
                            @if($isManager)
                                <div class="skeleton ml-auto h-9 w-9 shrink-0 rounded-[10px]"></div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>
</div>
