@section('page-title', isset($assignment) ? $assignment->title : 'รายละเอียดงาน')
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-blue-600 transition-colors">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <a href="{{ route('classroom.show', $classroom) }}" class="text-gray-500 hover:text-blue-600 transition-colors"
            title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 20) }}</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <span class="text-gray-800 font-semibold"
            title="{{ isset($assignment) ? $assignment->title : '' }}">{{ isset($assignment) ? \Illuminate\Support\Str::limit($assignment->title, 30) : '' }}</span>
    </nav>
@endsection
@endif
<div class="max-w-4xl mx-auto">
    {{-- Back link --}}
    <div class="skeleton h-5 w-16 mb-6"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Header + description card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="flex items-start min-w-0">
                            <div class="skeleton h-10 w-10 sm:h-12 sm:w-12 rounded-xl shrink-0"></div>
                            <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <div class="skeleton h-6 w-40 sm:w-56"></div>
                                    <div class="skeleton h-5 w-16 rounded-full shrink-0"></div>
                                </div>
                                <div class="flex items-center gap-3 mt-2">
                                    <div class="skeleton h-4 w-24"></div>
                                    <div class="skeleton h-3 w-16"></div>
                                </div>
                            </div>
                        </div>
                        @if(isset($classroom) ? ($classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin()) : auth()->user()->isTeacher())
                            <div class="skeleton h-8 w-8 rounded-full shrink-0"></div>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center gap-4 flex-wrap">
                        <div class="skeleton h-4 w-36"></div>
                        <div class="skeleton h-4 w-14"></div>
                        <div class="skeleton h-4 w-16"></div>
                    </div>
                </div>

                {{-- Description body --}}
                <div class="p-6 space-y-3">
                    <div class="skeleton h-4 w-full"></div>
                    <div class="skeleton h-4 w-11/12"></div>
                    <div class="skeleton h-4 w-3/4"></div>
                </div>
            </div>

            {{-- List block: teacher submissions table / student comment area --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="skeleton h-6 w-32"></div>
                    <div class="skeleton h-4 w-20"></div>
                </div>
                <div class="space-y-3">
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2.5">
                            <div class="skeleton h-8 w-8 rounded-full shrink-0"></div>
                            <div class="flex-1 space-y-1.5 min-w-0">
                                <div class="skeleton h-4 w-1/3"></div>
                                <div class="skeleton h-3 w-1/4"></div>
                            </div>
                            <div class="skeleton h-6 w-16 rounded-full shrink-0"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Sidebar: submission / info card --}}
        <div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden sticky top-0">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="skeleton h-6 w-28"></div>
                        <div class="skeleton h-5 w-20 rounded-full"></div>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    <div class="skeleton h-24 w-full rounded-lg"></div>
                    <div class="skeleton h-10 w-full rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>
</div>
