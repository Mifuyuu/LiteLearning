@section('page-title', 'รอตรวจ')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)]">
        <div class="p-6 lg:p-8">
            {{-- Header skeleton --}}
            <div class="space-y-2">
                <div class="skeleton h-9 w-32"></div>
                <div class="skeleton h-4 w-56"></div>
            </div>

            {{-- Filters skeleton --}}
            <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="skeleton h-10 w-full sm:w-64 rounded-lg"></div>
                <div class="skeleton h-10 w-full sm:w-44 rounded-xl"></div>
                <div class="flex items-center gap-2 sm:ml-auto">
                    <div class="skeleton h-8 w-16 rounded-lg"></div>
                    <div class="skeleton h-8 w-14 rounded-lg"></div>
                </div>
            </div>
        </div>

        {{-- Results skeleton --}}
        <div class="border-t border-[#dedee5] divide-y divide-gray-100">
            @for($i = 0; $i < 8; $i++)
                <div class="flex items-center p-4 gap-3">
                    <div class="skeleton h-10 w-10 rounded-full shrink-0"></div>
                    <div class="flex-1 space-y-1.5">
                        <div class="skeleton h-4 w-32"></div>
                        <div class="skeleton h-3 w-48"></div>
                    </div>
                    <div class="text-right space-y-1 mr-4">
                        <div class="skeleton h-5 w-14 rounded-full"></div>
                        <div class="skeleton h-3 w-16"></div>
                    </div>
                    <div class="skeleton h-8 w-20 rounded-lg shrink-0"></div>
                </div>
            @endfor
        </div>
    </div>
</div>
