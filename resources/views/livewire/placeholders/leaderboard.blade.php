@section('page-title', 'กระดานผู้นำ')
<div class="max-w-4xl mx-auto space-y-4">

    {{-- Podium Wrap --}}
    <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] px-6 pt-8 pb-0 overflow-hidden">
        {{-- Podium Mockup: 2nd | 1st | 3rd --}}
        <div class="flex items-end justify-center gap-3">
            {{-- 2nd Place --}}
            <div class="flex flex-col items-center w-1/3 max-w-[160px]">
                <div class="skeleton h-14 w-14 rounded-full mb-2"></div>
                <div class="skeleton h-5 w-20 mb-1"></div>
                <div class="skeleton h-4 w-12 mb-3"></div>
                <div class="skeleton w-full h-[100px] rounded-t-xl"></div>
            </div>

            {{-- 1st Place --}}
            <div class="flex flex-col items-center w-1/3 max-w-[180px]">
                <div class="skeleton h-5 w-5 mb-1"></div>
                <div class="skeleton h-16 w-16 sm:h-20 sm:w-20 rounded-full mb-2"></div>
                <div class="skeleton h-5 w-24 mb-1"></div>
                <div class="skeleton h-4 w-12 mb-3"></div>
                <div class="skeleton w-full h-[145px] rounded-t-xl"></div>
            </div>

            {{-- 3rd Place --}}
            <div class="flex flex-col items-center w-1/3 max-w-[160px]">
                <div class="skeleton h-12 w-12 rounded-full mb-2"></div>
                <div class="skeleton h-5 w-20 mb-1"></div>
                <div class="skeleton h-4 w-12 mb-3"></div>
                <div class="skeleton w-full h-[72px] rounded-t-xl"></div>
            </div>
        </div>
    </div>

    {{-- List rows --}}
    <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6 space-y-3">
        @for($i = 0; $i < 6; $i++)
            <div class="flex items-center justify-between py-2 border-b border-[#f2eff5] last:border-0">
                <div class="flex items-center gap-4">
                    <div class="skeleton h-5 w-5 rounded-full"></div>
                    <div class="skeleton h-10 w-10 rounded-full"></div>
                    <div class="skeleton h-4.5 w-32"></div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="skeleton h-4 w-16"></div>
                    <div class="skeleton h-6 w-12 rounded-full"></div>
                </div>
            </div>
        @endfor
    </div>
</div>
