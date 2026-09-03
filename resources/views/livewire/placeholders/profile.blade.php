@section('page-title', 'โปรไฟล์')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- Cover --}}
        <div class="skeleton h-48 w-full rounded-none"></div>

        {{-- Header info --}}
        <div class="px-5 pb-6 lg:px-7">
            <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-start sm:gap-20">
                <div class="-mt-10 sm:-mt-12 relative inline-block shrink-0 self-center sm:ml-16">
                    <div class="skeleton h-36 w-36 rounded-full border-4 border-white"></div>
                </div>
                <div class="min-w-0 pb-1 text-center sm:pt-4 sm:text-left space-y-3 flex-1">
                    <div class="skeleton h-9 w-48"></div>
                    <div class="skeleton h-5 w-24 rounded-full"></div>
                    <div class="skeleton h-4 w-full"></div>
                    <div class="skeleton h-4 w-3/4"></div>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="p-5 pt-0 lg:px-7 space-y-6">
            {{-- Level progress placeholder --}}
            <div class="rounded-xl border border-[#dedee5] bg-white p-4 sm:p-5 flex items-center gap-4 sm:gap-5">
                <div class="skeleton h-16 w-16 sm:h-18 sm:w-18 rounded-full shrink-0"></div>
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="flex justify-between items-center">
                        <div class="skeleton h-3.5 w-28"></div>
                        <div class="skeleton h-5 w-24 rounded-lg"></div>
                    </div>
                    <div class="skeleton h-8 w-full rounded-xl"></div>
                    <div class="skeleton h-3 w-40"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="skeleton h-20 rounded-xl"></div>
                <div class="skeleton h-20 rounded-xl"></div>
                <div class="skeleton h-20 rounded-xl"></div>
                <div class="skeleton h-20 rounded-xl"></div>
            </div>

            {{-- Chart placeholder --}}
            <div>
                <div class="skeleton h-5 w-56 mb-2"></div>
                <div class="skeleton h-24 w-full rounded-xl"></div>
                <div class="skeleton h-8 w-full rounded-xl mt-4"></div>
            </div>

            <div class="border-t border-[#dedee5]"></div>

            {{-- Achievements --}}
            <div>
                <div class="flex justify-between mb-4">
                    <div class="skeleton h-6 w-48"></div>
                    <div class="skeleton h-6 w-20 rounded-full"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                    @for($i = 0; $i < 5; $i++)
                        <div class="skeleton h-36 rounded-xl"></div>
                    @endfor
                </div>
            </div>

            <div class="border-t border-[#dedee5]"></div>

            {{-- Classrooms --}}
            <div>
                <div class="flex justify-between mb-4">
                    <div class="skeleton h-6 w-32"></div>
                    <div class="skeleton h-5 w-20"></div>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    @for($i = 0; $i < 2; $i++)
                        <div class="skeleton h-48 rounded-xl"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
