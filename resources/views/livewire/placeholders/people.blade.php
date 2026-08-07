@section('page-title', (isset($classroom) ? ('สมาชิก' . ' - ' . $classroom->name) : 'สมาชิก'))
<div class="space-y-6">

    {{-- Sort filter bar mockup --}}
    <section class="flex flex-wrap items-center gap-2 rounded-2xl border border-[#dedee5] bg-white p-2 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="skeleton h-9 w-24 rounded-[10px]"></div>
        <div class="skeleton h-9 w-24 rounded-[10px]"></div>
        <div class="skeleton h-9 w-20 rounded-[10px]"></div>
    </section>

    {{-- Grid columns --}}
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        {{-- Teachers card --}}
        <section class="rounded-2xl border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] space-y-6">
            <div class="flex items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="skeleton h-3.5 w-24"></div>
                    <div class="skeleton h-5 w-48"></div>
                </div>
                <div class="skeleton h-6 w-8 rounded-[8px]"></div>
            </div>

            <div class="space-y-3">
                @for($i = 0; $i < 2; $i++)
                    <div class="rounded-[12px] border border-[#dedee5] bg-[var(--ll-blue-faint)] p-4">
                        <div class="flex items-center gap-3">
                            <div class="skeleton h-11 w-11 rounded-2xl shrink-0"></div>
                            <div class="space-y-2 min-w-0 flex-1">
                                <div class="skeleton h-4 w-1/3"></div>
                                <div class="skeleton h-3 w-1/2"></div>
                            </div>
                            <div class="skeleton h-5 w-12 rounded-[6px] shrink-0"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </section>

        {{-- Students card --}}
        <section class="rounded-2xl border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] space-y-6">
            <div class="flex items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="skeleton h-3.5 w-24"></div>
                    <div class="skeleton h-5 w-32"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="skeleton h-6 w-8 rounded-[8px]"></div>
                    @if(auth()->user()->isTeacher())
                        <div class="skeleton h-8 w-24 rounded-[10px]"></div>
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                @for($i = 0; $i < 4; $i++)
                    <div class="flex items-center justify-between py-3 border-b border-[#f2eff5] last:border-0">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="skeleton h-10 w-10 rounded-2xl shrink-0"></div>
                            <div class="space-y-1.5 min-w-0 flex-1">
                                <div class="skeleton h-4 w-1/3"></div>
                                <div class="skeleton h-3 w-1/2"></div>
                            </div>
                        </div>
                        <div class="skeleton h-6 w-16 rounded-[7px] shrink-0"></div>
                    </div>
                @endfor
            </div>
        </section>
    </div>
</div>
