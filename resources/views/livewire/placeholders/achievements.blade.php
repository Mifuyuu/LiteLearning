@section('page-title', 'ความสำเร็จ')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- Header --}}
        <div class="relative p-6 lg:p-8">
            <div class="skeleton absolute right-4 top-4 hidden h-36 w-36 rounded-full sm:block lg:right-8 lg:top-8"></div>
            <div class="space-y-2">
                <div class="skeleton h-10 w-64"></div>
                <div class="skeleton h-4 w-full sm:w-96"></div>
            </div>
            <div class="mt-6 max-w-md space-y-2">
                <div class="flex items-center justify-between">
                    <div class="skeleton h-4 w-28"></div>
                    <div class="skeleton h-3.5 w-20"></div>
                </div>
                <div class="skeleton h-5 w-full rounded-full"></div>
            </div>
        </div>

        {{-- Grid --}}
        <div class="p-6 lg:p-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @for($i = 0; $i < 8; $i++)
                    <article class="rounded-xl border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                        <div class="flex gap-4">
                            <div class="skeleton h-16 w-16 shrink-0 rounded-lg"></div>
                            <div class="min-w-0 flex-1 space-y-3">
                                <div class="skeleton h-5 w-full"></div>
                                <div class="skeleton h-3.5 w-full"></div>
                                <div class="skeleton h-3 w-2/3"></div>
                                <div class="flex justify-end gap-2">
                                    <div class="skeleton h-6 w-14 rounded-md"></div>
                                    <div class="skeleton h-6 w-14 rounded-md"></div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endfor
            </div>
        </div>

    </div>
</div>
