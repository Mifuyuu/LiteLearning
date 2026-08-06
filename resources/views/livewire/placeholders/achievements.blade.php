@section('page-title', 'ความสำเร็จ')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- Header --}}
        <div class="grid gap-6 p-6 lg:grid-cols-[1fr_320px] lg:p-8">
            <div class="space-y-4">
                <div class="space-y-2">
                    <div class="skeleton h-9 w-64"></div>
                    <div class="skeleton h-4 w-full sm:w-96"></div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-end justify-between">
                    <div class="space-y-1">
                        <div class="skeleton h-3 w-16"></div>
                        <div class="skeleton h-8 w-20"></div>
                    </div>
                    <div class="skeleton h-12 w-12 rounded-[10px]"></div>
                </div>
                <div class="skeleton h-3 w-full rounded-full"></div>
                <div class="skeleton h-3.5 w-24"></div>
            </div>
        </div>

        <div class="border-t border-[#dedee5]"></div>

        {{-- Grid --}}
        <div class="p-6 lg:p-8">
            <div class="grid grid-cols-2 gap-4">
                @for($i = 0; $i < 8; $i++)
                    <article class="rounded-xl border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                        <div class="flex gap-4">
                            <div class="skeleton h-16 w-16 shrink-0 rounded-[12px]"></div>
                            <div class="min-w-0 flex-1 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="skeleton h-5 w-32 flex-1"></div>
                                    <div class="skeleton h-5 w-16 rounded-[6px] shrink-0"></div>
                                </div>
                                <div class="skeleton h-3.5 w-full"></div>
                                <div class="skeleton h-3 w-2/3"></div>
                                <div class="skeleton h-2 w-full rounded-full"></div>
                            </div>
                        </div>
                    </article>
                @endfor
            </div>
        </div>

    </div>
</div>
