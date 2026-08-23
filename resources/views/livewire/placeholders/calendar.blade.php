@section('page-title', 'ปฏิทิน')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)]">
        <div class="p-6 lg:p-8">
            <div class="space-y-2">
                <div class="skeleton h-9 w-32"></div>
                <div class="skeleton h-4 w-64"></div>
            </div>
        </div>

        <div class="border-t border-[#dedee5] space-y-6 p-6 lg:p-8">
            @foreach([2, 1] as $rowCount)
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="skeleton h-3 w-16"></div>
                        <div class="skeleton h-3 w-24"></div>
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <div class="skeleton h-3 w-14 rounded-full"></div>
                    </div>

                    <div class="space-y-2">
                        @for($i = 0; $i < $rowCount; $i++)
                            <div class="rounded-lg border border-[#dedee5] bg-white flex items-center gap-4 p-4">
                                <div class="skeleton h-3 w-3 rounded-full shrink-0"></div>
                                <div class="flex-1 min-w-0 space-y-1.5">
                                    <div class="skeleton h-4 w-2/3"></div>
                                    <div class="skeleton h-3 w-1/3"></div>
                                </div>
                                <div class="skeleton h-4 w-10 shrink-0"></div>
                                <div class="skeleton h-4 w-4 rounded-full shrink-0"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
