@section('page-title', 'แดชบอร์ด')
<div class="max-w-4xl mx-auto">
    <style>
        /* Mobile (default) */
        .activity-grid-cols {
            grid-template-columns: repeat(26, 0.5rem) !important;
            gap: 1px !important;
        }
        .activity-cell {
            width: 0.5rem !important;
            height: 0.5rem !important;
            border-radius: 0px !important;
        }

        /* Tablets & Medium screens */
        @media (min-width: 640px) {
            .activity-grid-cols {
                grid-template-columns: repeat(26, 0.75rem) !important;
                gap: 0.25rem !important;
            }
            .activity-cell {
                width: 0.75rem !important;
                height: 0.75rem !important;
                border-radius: 3px !important;
            }
        }

        /* Large screens (desktop) */
        @media (min-width: 1280px) {
            .activity-grid-cols {
                grid-template-columns: repeat(26, minmax(0, 1fr)) !important;
                gap: 0.25rem !important;
            }
            .activity-cell {
                width: 100% !important;
                height: auto !important;
                border-radius: 3px !important;
            }
        }
    </style>

    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)]">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4 p-5 lg:p-7">
            <div class="space-y-2">
                <div class="skeleton h-8 w-48"></div>
                <div class="skeleton h-4 w-64"></div>
            </div>
            {{-- <div class="skeleton h-9 w-24 rounded-lg shrink-0"></div> --}}
        </div>

        {{-- Primary metric + Quick stats --}}
        <div class="mx-5 border-t border-[#dedee5] py-5 lg:mx-7">
            <div class="grid gap-6 sm:grid-cols-2 sm:divide-x sm:divide-[#dedee5]">
                <div class="sm:pr-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="skeleton h-3 w-20"></div>
                            <div class="skeleton h-10 w-12"></div>
                        </div>
                        <div class="skeleton h-6 w-24 rounded-sm"></div>
                    </div>
                    <div class="skeleton h-2 w-full rounded-full mt-5"></div>
                    <div class="skeleton h-3 w-40 mt-2"></div>
                </div>

                <div class="sm:pl-6">
                    <div class="skeleton h-5 w-24"></div>
                    <div class="mt-3 space-y-2">
                        @for($i = 0; $i < 4; $i++)
                            <div class="flex items-center justify-between gap-3 rounded-lg bg-[#f7f5f9] px-3 py-2.5">
                                <span class="flex items-center gap-2.5">
                                    <div class="skeleton h-8 w-8 rounded-lg shrink-0"></div>
                                    <div class="skeleton h-3 w-16"></div>
                                </span>
                                <div class="skeleton h-4 w-8 shrink-0"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity heatmap --}}
        <div class="mx-5 border-t border-[#dedee5] py-5 lg:mx-7">
            <div class="flex justify-between items-start">
                <div class="space-y-2">
                    <div class="skeleton h-3 w-28"></div>
                    <div class="skeleton h-5 w-48"></div>
                    <div class="skeleton h-3.5 w-64"></div>
                </div>
                <div class="skeleton h-7 w-20 rounded-md"></div>
            </div>
            <div class="mt-5 overflow-visible">
                <div class="grid w-max grid-cols-[24px_max-content] gap-2 xl:w-full xl:min-w-0 xl:grid-cols-[24px_1fr]">
                    <div></div>
                    <div class="activity-grid-cols grid">
                        @for($i = 0; $i < 6; $i++)
                            <div class="skeleton h-3 w-8" style="grid-column: {{ ($i * 4) + 1 }};"></div>
                        @endfor
                    </div>
                    <div class="grid grid-rows-7 gap-1 text-[9px]">
                        <div class="skeleton h-2.5 w-4"></div>
                        <div></div>
                        <div class="skeleton h-2.5 w-4"></div>
                        <div></div>
                        <div class="skeleton h-2.5 w-4"></div>
                    </div>
                    <div class="activity-grid-cols grid grid-flow-col grid-rows-7 gap-1">
                        @for($i = 0; $i < 7 * 26; $i++)
                            <div class="activity-cell skeleton aspect-square size-3 rounded-[3px]"></div>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2">
                <div class="skeleton h-12 rounded-[11px]"></div>
                <div class="skeleton h-12 rounded-[11px]"></div>
                <div class="skeleton h-12 rounded-[11px]"></div>
            </div>
        </div>
    </div>
</div>
