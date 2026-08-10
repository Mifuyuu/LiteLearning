@section('page-title', 'รอตรวจ')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header skeleton --}}
    <div>
        <div class="skeleton h-8 w-24 mb-2"></div>
        <div class="skeleton h-4 w-56"></div>
    </div>

    {{-- Filters skeleton --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="skeleton h-10 w-full sm:w-64 rounded-lg"></div>
        <div class="skeleton h-10 w-full sm:w-44 rounded-xl"></div>
        <div class="flex items-center gap-2 ml-auto">
            <div class="skeleton h-8 w-16 rounded-lg"></div>
            <div class="skeleton h-8 w-14 rounded-lg"></div>
        </div>
    </div>

    {{-- Results skeleton --}}
    <div class="bg-white rounded-xl border border-[#dedee5]">
        <div class="divide-y divide-gray-100">
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
