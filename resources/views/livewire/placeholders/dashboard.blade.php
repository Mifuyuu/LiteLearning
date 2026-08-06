@section('page-title', 'แดชบอร์ด')
<div class="flex min-h-full flex-col gap-3 xl:h-[calc(100vh-3rem)] xl:min-h-[560px] xl:overflow-hidden">
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
    {{-- Header --}}
    <header class="flex shrink-0 items-center justify-between gap-4">
        <div class="space-y-2">
            <div class="skeleton h-8 w-48"></div>
            <div class="skeleton h-4 w-64"></div>
        </div>
    </header>

    {{-- Main Grid --}}
    <div class="grid min-h-0 flex-1 gap-3 xl:grid-cols-[minmax(220px,0.78fr)_minmax(380px,1.55fr)_minmax(220px,0.78fr)]">
        {{-- Left Column --}}
        <div class="grid min-h-0 gap-3 xl:grid-rows-[180px_minmax(0,1fr)]">
            {{-- Level Card --}}
            <div class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body gap-0 p-5">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="skeleton h-3 w-20"></div>
                            <div class="skeleton h-10 w-12"></div>
                        </div>
                        <div class="skeleton h-6 w-24 rounded-lg"></div>
                    </div>
                    <div class="skeleton h-2 w-full rounded-full mt-5"></div>
                    <div class="skeleton h-3 w-40 mt-2"></div>
                </div>
            </div>

            {{-- Up Next --}}
            <div class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body min-h-0 gap-0 p-4">
                    <div class="flex shrink-0 items-center justify-between gap-3">
                        <div class="skeleton h-5 w-24"></div>
                    </div>
                    <div class="mt-4 space-y-4">
                        @for($i = 0; $i < 3; $i++)
                            <div class="flex items-center gap-3 py-1">
                                <div class="skeleton h-9 w-9 rounded-[10px]"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="skeleton h-4 w-5/6"></div>
                                    <div class="skeleton h-3 w-1/2"></div>
                                </div>
                                <div class="skeleton h-5 w-10 rounded-md"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Center Column (Activity Heatmap) --}}
        <div class="card card-border border-[#dedee5] bg-white">
            <div class="card-body flex h-full min-h-0 flex-col gap-0 p-4 sm:p-5">
                <div class="flex justify-between items-start">
                    <div class="space-y-2">
                        <div class="skeleton h-3 w-28"></div>
                        <div class="skeleton h-5 w-48"></div>
                        <div class="skeleton h-3.5 w-64"></div>
                    </div>
                    <div class="skeleton h-7 w-20 rounded-md"></div>
                </div>
                {{-- Grid Mockup --}}
                <div class="mt-5 min-h-0 flex-1 overflow-visible">
                    <div class="grid xl:min-w-0 w-max xl:w-full grid-cols-[24px_max-content] xl:grid-cols-[24px_1fr] gap-2">
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
                <div class="mt-4 grid grid-cols-3 gap-2 shrink-0">
                    <div class="skeleton h-12 rounded-[11px]"></div>
                    <div class="skeleton h-12 rounded-[11px]"></div>
                    <div class="skeleton h-12 rounded-[11px]"></div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="grid min-h-0 gap-3 xl:grid-rows-[340px_minmax(0,1fr)]">
            {{-- Profile --}}
            <div class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body items-center justify-center gap-0 p-5 text-center">
                    <div class="skeleton h-20 w-20 rounded-full"></div>
                    <div class="skeleton h-5 w-32 mt-4 mx-auto"></div>
                    <div class="skeleton h-3 w-40 mt-1 mx-auto"></div>
                    <div class="flex gap-2 mt-4 justify-center">
                        <div class="skeleton h-6 w-16 rounded-full"></div>
                        <div class="skeleton h-6 w-24 rounded-full"></div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card card-border overflow-hidden border-[#dedee5] bg-white">
                <div class="card-body gap-0 p-4">
                    <div class="skeleton h-5 w-24 mb-3"></div>
                    <div class="grid grid-cols-2 gap-2 flex-1">
                        @for($i = 0; $i < 4; $i++)
                            <div class="flex flex-col justify-between rounded-[11px] bg-[#f7f5f9] p-3 min-h-[80px]">
                                <div class="skeleton h-8 w-8 rounded-[9px]"></div>
                                <div class="space-y-1 mt-2">
                                    <div class="skeleton h-5 w-10"></div>
                                    <div class="skeleton h-2.5 w-16"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
