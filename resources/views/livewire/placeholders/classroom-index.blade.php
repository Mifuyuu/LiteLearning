@section('page-title', 'ชั้นเรียนของฉัน')
<div class="max-w-4xl mx-auto">
    {{-- Search / filter bar mockup --}}
    <div class="bg-white border border-[#dedee5] rounded-xl shadow-[rgba(0,0,0,0.03)_0px_4px_24px] px-4 py-3">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gray-50 border border-[#dedee5] flex-1 sm:flex-none">
                <x-icon name="magnifying-glass" class="text-[#9497a9] h-4 w-4 shrink-0" />
                <div class="skeleton h-5 w-44"></div>
            </div>
            <div class="skeleton h-5 w-full sm:w-28 sm:ml-auto"></div>
        </div>
    </div>

    {{-- List rows --}}
    <div class="space-y-4 mt-4">
        @for($i = 0; $i < 5; $i++)
            <div class="flex items-center gap-5 rounded-xl border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5 ring-2 ring-transparent">
                <div class="skeleton w-24 h-24 rounded-full shrink-0"></div>
                <div class="flex-1 min-w-0 space-y-2">
                    <div class="skeleton h-5 w-5/6"></div>
                    <div class="skeleton h-3 w-1/2"></div>
                    <div class="skeleton h-3 w-2/3"></div>
                </div>
                <div class="skeleton h-5 w-5 rounded shrink-0"></div>
            </div>
        @endfor
    </div>
</div>
