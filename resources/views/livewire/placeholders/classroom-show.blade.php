@section('page-title', isset($classroom) ? $classroom->name : 'ห้องเรียน')
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]" title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 25, '..') }}</span>
    </nav>
@endsection
@endif
@php
    $isManager = isset($classroom) ? $classroom->canManageClassroom(auth()->user()) : auth()->user()->isTeacher();
@endphp
<div class="max-w-4xl mx-auto">
    <section class="overflow-hidden rounded-xl border-3 border-[#dedee5] bg-white min-h-[calc(100vh-3rem)]">

        {{-- Theme banner --}}
        <div class="skeleton h-32 w-full rounded-none sm:h-40"></div>

        {{-- Header --}}
        <div class="p-5 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <div class="skeleton h-3 w-16"></div>
                    <div class="skeleton mt-2 h-8 w-64 sm:h-9"></div>
                    <div class="skeleton mt-2 h-4 w-5/6 max-w-3xl"></div>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <div class="skeleton h-7 w-28 rounded-lg"></div>
                        <div class="skeleton h-7 w-20 rounded-lg"></div>
                        <div class="skeleton h-7 w-24 rounded-lg"></div>
                    </div>
                </div>

                <div class="flex shrink-0 flex-wrap gap-2">
                    @if($isManager)
                        <div class="skeleton h-10 w-24 rounded-[10px]"></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick links: 3 sections divided inside the box --}}
        <div class="grid divide-y divide-[#f2eff5] border-t border-[#f2eff5] lg:grid-cols-3 lg:divide-x lg:divide-y-0">
            @for($i = 0; $i < 3; $i++)
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="skeleton h-10 w-10 rounded-[10px]"></div>
                        <div class="skeleton h-8 w-8"></div>
                    </div>
                    <div class="skeleton mt-4 h-5 w-28"></div>
                    <div class="skeleton mt-1 h-4 w-full"></div>
                </div>
            @endfor
        </div>

        {{-- กระดานสนทนา --}}
        <div class="border-t border-[#f2eff5] p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="skeleton h-3 w-20"></div>
                    <div class="skeleton mt-1 h-6 w-32"></div>
                </div>
                <div class="skeleton h-4 w-16"></div>
            </div>
            <div class="mt-4 space-y-3">
                @for($i = 0; $i < 3; $i++)
                    <div class="rounded-[10px] border border-[#dedee5] px-3 py-3">
                        <div class="flex items-start gap-3">
                            <div class="skeleton h-8 w-8 shrink-0 rounded-lg"></div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="skeleton h-4 w-1/3"></div>
                                    <div class="skeleton h-3 w-12 shrink-0"></div>
                                </div>
                                <div class="skeleton mt-2 h-3 w-5/6"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- ต้องดูแล --}}
        <div class="border-t border-[#f2eff5] p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="skeleton h-3 w-20"></div>
                    <div class="skeleton mt-1 h-6 w-32"></div>
                </div>
                <div class="skeleton h-4 w-16"></div>
            </div>
            <div class="mt-4 space-y-3">
                @for($i = 0; $i < 4; $i++)
                    <div class="rounded-[10px] border border-[#dedee5] px-3 py-3">
                        <div class="skeleton h-4 w-2/3"></div>
                        <div class="skeleton mt-1 h-3 w-1/3"></div>
                    </div>
                @endfor
            </div>
        </div>
    </section>
</div>
