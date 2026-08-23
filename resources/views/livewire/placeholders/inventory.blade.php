@section('page-title', 'คลังเก็บของ')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh_-_3rem)]">
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/Inventory.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-[calc(100%_-_2rem)] w-auto select-none object-contain sm:block lg:right-8" />
            <div class="relative max-w-sm space-y-2">
                <div class="skeleton h-9 w-48"></div>
                <div class="skeleton h-4 w-64"></div>
                <div class="skeleton h-14 w-40 rounded-xl mt-4"></div>
            </div>
        </div>

        <div class="border-t border-[#dedee5] space-y-8 p-6 lg:p-8">
            <div>
                <div class="skeleton h-6 w-32 mb-6"></div>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @for($i = 0; $i < 4; $i++)
                        <div class="border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                            <div class="h-16 flex items-center justify-center">
                                <div class="skeleton h-7 w-24"></div>
                            </div>
                            <div class="skeleton h-4 w-20 mt-2"></div>
                            <div class="skeleton h-10 w-full mt-1"></div>
                            <div class="mt-4 w-full">
                                <div class="skeleton h-10 w-full rounded-lg"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div>
                <div class="skeleton h-6 w-32 mb-6"></div>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @for($i = 0; $i < 3; $i++)
                        <div class="border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                            <div class="h-20 flex items-center justify-center mb-2 mt-4">
                                <div class="skeleton h-16 w-16 rounded-full"></div>
                            </div>
                            <div class="skeleton h-4 w-20 mt-2"></div>
                            <div class="skeleton h-10 w-full mt-1"></div>
                            <div class="mt-4 w-full">
                                <div class="skeleton h-10 w-full rounded-lg"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
