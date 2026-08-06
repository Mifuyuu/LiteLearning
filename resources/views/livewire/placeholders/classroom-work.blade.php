@section('page-title', (isset($classroom) ? ('งานในชั้นเรียน' . ' - ' . $classroom->name) : 'งานในชั้นเรียน'))
<div class="space-y-6">
    @if(isset($classroom))
        @include('livewire.classroom.partials.subnav', ['classroom' => $classroom])
    @endif

    {{-- Filter bar mockup --}}
    <section class="flex flex-wrap items-center gap-2 rounded-2xl border border-[#dedee5] bg-white p-2 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="skeleton h-9 w-16 rounded-[10px]"></div>
        <div class="skeleton h-9 w-24 rounded-[10px]"></div>
        <div class="skeleton h-9 w-24 rounded-[10px]"></div>
        @if(auth()->user()->isTeacher())
            <div class="skeleton h-9 w-36 rounded-[12px] ml-auto"></div>
        @endif
    </section>

    {{-- Work content mockup --}}
    <div class="space-y-6">
        @for($t = 0; $t < 2; $t++)
            <section class="space-y-5">
                <div class="skeleton h-4 w-28"></div>
                <section class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="skeleton h-5 w-32"></div>
                        <div class="h-px flex-1 bg-[#dedee5]"></div>
                    </div>
                    <div class="space-y-3">
                        @for($i = 0; $i < 2; $i++)
                            <div class="rounded-xl border border-[#dedee5] bg-white p-4 flex items-center justify-between gap-4 shadow-sm">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="skeleton h-9 w-9 rounded-[10px] shrink-0"></div>
                                    <div class="flex-1 space-y-2 min-w-0">
                                        <div class="skeleton h-4 w-1/3"></div>
                                        <div class="skeleton h-3 w-1/4"></div>
                                    </div>
                                </div>
                                <div class="skeleton h-6 w-16 rounded-[7px] shrink-0"></div>
                            </div>
                        @endfor
                    </div>
                </section>
            </section>
        @endfor
    </div>
</div>
