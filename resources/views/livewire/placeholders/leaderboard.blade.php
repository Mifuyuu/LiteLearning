@section('page-title', 'กระดานผู้นำ')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)]">
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/trophy.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-[calc(100%-2rem)] w-auto select-none object-contain sm:block lg:right-8" />
            <div class="relative max-w-sm space-y-2">
                <div class="skeleton h-9 w-56"></div>
                <div class="skeleton h-4 w-64"></div>
            </div>
        </div>

        <div class="border-t border-[#dedee5] p-6 lg:p-8">
            {{-- Podium Mockup: 2nd | 1st | 3rd --}}
            <div class="flex items-end justify-center gap-3">
                {{-- 2nd Place --}}
                <div class="flex flex-col items-center w-1/3 max-w-40">
                    <div class="skeleton h-14 w-14 rounded-full mb-2"></div>
                    <div class="skeleton h-5 w-20 mb-1"></div>
                    <div class="skeleton h-4 w-12 mb-3"></div>
                    <div class="skeleton w-full h-25 rounded-t-xl rounded-b-none"></div>
                </div>

                {{-- 1st Place --}}
                <div class="flex flex-col items-center w-1/3 max-w-45">
                    <div class="skeleton h-16 w-16 sm:h-20 sm:w-20 rounded-full mb-2"></div>
                    <div class="skeleton h-5 w-24 mb-1"></div>
                    <div class="skeleton h-4 w-12 mb-3"></div>
                    <div class="skeleton w-full h-36.25 rounded-t-xl rounded-b-none"></div>
                </div>

                {{-- 3rd Place --}}
                <div class="flex flex-col items-center w-1/3 max-w-40">
                    <div class="skeleton h-12 w-12 rounded-full mb-2"></div>
                    <div class="skeleton h-5 w-20 mb-1"></div>
                    <div class="skeleton h-4 w-12 mb-3"></div>
                    <div class="skeleton w-full h-18 rounded-t-xl rounded-b-none"></div>
                </div>
            </div>

            {{-- List rows --}}
            <div class="mt-8 -mx-6 lg:-mx-8 border-t border-[#dedee5]">
                @for($i = 0; $i < 6; $i++)
                    <div class="flex items-center gap-3 px-6 lg:px-8 py-3 border-b border-[#dedee5] last:border-0">
                        <div class="skeleton h-4 w-6 shrink-0"></div>
                        <div class="skeleton h-9 w-9 rounded-full shrink-0"></div>
                        <div class="flex-1 min-w-0 space-y-1.5">
                            <div class="skeleton h-4 w-32"></div>
                            <div class="skeleton h-3 w-16"></div>
                        </div>
                        <div class="skeleton h-4 w-10 shrink-0"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
