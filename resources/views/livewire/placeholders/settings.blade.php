@section('page-title', 'ตั้งค่า')

<div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

    {{-- Page Header --}}
    <div class="p-6 lg:p-8">
        <div class="skeleton h-9 w-40"></div>
        <div class="skeleton h-4 w-64 mt-3"></div>
    </div>

    <div class="border-t border-[#dedee5] p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 md:divide-x md:divide-gray-200">

            {{-- Left: Avatar + Cover Image + Notifications --}}
            <div class="space-y-6 md:pr-6">

                {{-- Avatar --}}
                <div>
                    <div class="skeleton h-5 w-28 mb-4"></div>
                    <div class="flex items-center gap-5">
                        <div class="skeleton h-20 w-20 rounded-full shrink-0"></div>
                        <div class="flex flex-col gap-2">
                            <div class="skeleton h-8 w-24 rounded-lg"></div>
                            <div class="skeleton h-8 w-24 rounded-lg"></div>
                        </div>
                    </div>
                </div>

                {{-- Cover Image --}}
                <div>
                    <div class="skeleton h-5 w-32 mb-4"></div>
                    <div class="skeleton h-32 w-full rounded-xl mb-3"></div>
                    <div class="flex gap-2">
                        <div class="skeleton h-8 w-24 rounded-lg"></div>
                        <div class="skeleton h-8 w-24 rounded-lg"></div>
                    </div>
                </div>

                {{-- Notifications --}}
                <div class="pt-8">
                    <div class="skeleton h-5 w-28 mb-4"></div>
                    <div class="space-y-3">
                        @for($i = 0; $i < 4; $i++)
                            <div class="flex items-center justify-between">
                                <div class="skeleton h-4 w-40"></div>
                                <div class="skeleton h-5 w-9 rounded-full"></div>
                            </div>
                        @endfor
                    </div>
                </div>

            </div>

            {{-- Right: Account Settings + Language + Privacy --}}
            <div class="space-y-6 md:pl-6">
                <div class="md:pt-6 space-y-6">

                    {{-- Account settings --}}
                    <div>
                        <div class="skeleton h-5 w-28 mb-4"></div>
                        <div class="space-y-5">

                            {{-- Name --}}
                            <div>
                                <div class="skeleton h-3 w-40 mb-2"></div>
                                <div class="flex items-center gap-2">
                                    <div class="skeleton h-10 flex-1 rounded-xl"></div>
                                    <div class="skeleton h-10 w-20 rounded-xl shrink-0"></div>
                                </div>
                            </div>

                            {{-- Username --}}
                            <div>
                                <div class="skeleton h-3 w-40 mb-2"></div>
                                <div class="flex items-center gap-2">
                                    <div class="skeleton h-10 flex-1 rounded-xl"></div>
                                    <div class="skeleton h-10 w-20 rounded-xl shrink-0"></div>
                                </div>
                                <div class="skeleton h-3 w-56 mt-2"></div>
                            </div>

                            {{-- Email (read-only) --}}
                            <div>
                                <div class="skeleton h-3 w-16 mb-2"></div>
                                <div class="skeleton h-10 w-full rounded-xl"></div>
                            </div>

                        </div>
                    </div>

                    {{-- Language & Region --}}
                    <div class="pt-8">
                        <div class="skeleton h-5 w-36 mb-4"></div>
                        <div class="space-y-3">
                            <div>
                                <div class="skeleton h-3 w-16 mb-2"></div>
                                <div class="skeleton h-10 w-full rounded-xl"></div>
                            </div>
                            <div>
                                <div class="skeleton h-3 w-20 mb-2"></div>
                                <div class="skeleton h-10 w-full rounded-xl"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Privacy --}}
                    <div>
                        <div class="skeleton h-5 w-32 mb-4"></div>
                        <div class="space-y-3">
                            @for($i = 0; $i < 2; $i++)
                                <div class="flex items-center justify-between">
                                    <div class="skeleton h-4 w-48"></div>
                                    <div class="skeleton h-5 w-9 rounded-full"></div>
                                </div>
                            @endfor
                        </div>
                        <div class="skeleton h-3 w-24 mt-3"></div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
