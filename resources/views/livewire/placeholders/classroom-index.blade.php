@section('page-title', auth()->user()->isTeacher() ? __('ชั้นเรียนของฉัน') : __('Classrooms'))
<div class="flex min-h-[calc(100dvh-6.75rem)] flex-col lg:min-h-[calc(100dvh-3rem)]">
    {{-- Search / filter bar mockup --}}
    <div class="bg-white border border-[#dedee5] rounded-2xl shadow-[rgba(0,0,0,0.03)_0px_4px_24px] px-4 py-3">
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Search box --}}
            <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-50 border border-[#dedee5] w-full sm:w-auto">
                <i class="fas fa-search text-[#9497a9] text-xs"></i>
                <div class="skeleton h-5 w-44"></div>
            </div>
            {{-- Count skeleton --}}
            <div class="skeleton h-4 w-20 ml-auto"></div>
        </div>
    </div>

    {{-- Classroom grid mockup --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-6 items-stretch mt-6">
        @for($i = 0; $i < 4; $i++)
            <div class="relative pt-20 h-full">
                {{-- Planet Skeleton --}}
                <div class="absolute top-7 left-1/2 -translate-x-1/2 z-10">
                    <div class="skeleton w-24 h-24 rounded-full"></div>
                </div>

                {{-- White card --}}
                <div class="rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] flex flex-col h-full bg-white">
                    {{-- Card body --}}
                    <div class="px-5 pt-16 pb-5 flex flex-col flex-1">
                        {{-- Name + badges skeleton --}}
                        <div class="mb-3 space-y-2">
                            <div class="skeleton h-5 w-5/6"></div>
                            <div class="skeleton h-3.5 w-1/2"></div>
                        </div>

                        <div class="h-px bg-[#dedee5] mb-3"></div>

                        {{-- Teacher skeleton --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="skeleton w-7 h-7 rounded-full shrink-0"></div>
                            <div class="skeleton h-3.5 w-24"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
