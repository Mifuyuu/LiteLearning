@section('page-title', isset($assignment) ? $assignment->title : 'รายละเอียดงาน')
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-[#686b82] hover:text-(--ll-blue) transition-colors">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" class="text-[#686b82] hover:text-(--ll-blue) transition-colors"
            title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 15, '..') }}</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="text-[#101114] font-semibold"
            title="{{ isset($assignment) ? $assignment->title : '' }}">{{ isset($assignment) ? \Illuminate\Support\Str::limit($assignment->title, 25, '..') : '' }}</span>
    </nav>
@endsection
@endif
@php
    $isManager = isset($classroom) ? ($classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin()) : auth()->user()->isTeacher();
@endphp
<div class="max-w-4xl mx-auto">
    <div class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">
        {{-- Header --}}
        <div class="p-4 sm:p-6 border-b border-[#dedee5]">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start min-w-0">
                    <div class="skeleton h-10 w-10 rounded-[10px] shrink-0"></div>
                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="skeleton h-6 w-48"></div>
                            <div class="skeleton h-5 w-16 rounded-full shrink-0"></div>
                        </div>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="skeleton h-4 w-24"></div>
                            <div class="skeleton h-3 w-16"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 ml-auto shrink-0">
                    @if($isManager)
                        <div class="skeleton h-10 w-10 rounded-[10px] shrink-0"></div>
                    @endif
                    <div class="skeleton h-10 w-10 rounded-[10px] shrink-0"></div>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-4 flex-wrap">
                <div class="skeleton h-4 w-36"></div>
            </div>
        </div>

        {{-- Description body --}}
        <div class="p-6 space-y-3 flex-1">
            <div class="skeleton h-4 w-full"></div>
            <div class="skeleton h-4 w-11/12"></div>
            <div class="skeleton h-4 w-3/4"></div>
        </div>

        {{-- ข้อมูลงาน stats (shown for everyone) --}}
        <div class="border-t border-[#dedee5] p-6">
            <div class="skeleton h-4 w-20 mb-3"></div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @for($i = 0; $i < 4; $i++)
                    <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                        <div class="skeleton h-3 w-16 mb-2"></div>
                        <div class="skeleton h-7 w-10"></div>
                    </div>
                @endfor
            </div>
        </div>

        @if(!$isManager)
            {{-- งานของคุณ student submission placeholder --}}
            <div class="border-t border-[#dedee5] p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="skeleton h-6 w-24"></div>
                    <div class="skeleton h-5 w-20 rounded-full"></div>
                </div>
                <div class="skeleton h-28 w-full max-w-md rounded-xl"></div>
            </div>
        @endif

        @if($isManager && !(isset($assignment) && ($assignment->type === 'attendance')) )
            {{-- งานนักเรียน list --}}
            <div class="border-t border-[#dedee5]">
                <div class="p-4 sm:p-6 border-b border-[#dedee5]">
                    <div class="skeleton h-6 w-28"></div>
                    <div class="flex gap-4 mt-2">
                        <div class="skeleton h-4 w-20"></div>
                        <div class="skeleton h-4 w-24"></div>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center justify-between p-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="skeleton h-9 w-9 rounded-full shrink-0"></div>
                                <div class="space-y-1.5">
                                    <div class="skeleton h-4 w-32"></div>
                                    <div class="skeleton h-3 w-20"></div>
                                </div>
                            </div>
                            <div class="skeleton h-7 w-20 rounded-lg shrink-0"></div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>
</div>
