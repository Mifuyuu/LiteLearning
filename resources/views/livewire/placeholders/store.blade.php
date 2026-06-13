@section('page-title', __('store.title'))
<div class="space-y-6">
    <style>
        /* Light-toned custom scrollbar styles for store horizontal containers */
        .store-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .store-scrollbar::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 9999px;
        }
        .store-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 9999px;
            transition: background 0.2s ease;
        }
        .store-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }
        .store-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb #f3f4f6;
        }
    </style>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="skeleton h-8 w-48"></div>
            <div class="skeleton h-4 w-64 mt-2"></div>
        </div>
        <div class="skeleton h-10 w-36 rounded-[8px]"></div>
    </div>

    {{-- Combined Single Card Container --}}
    <div class="bg-white rounded-xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6 mb-6">
        <!-- Name Colors Section Mockup -->
        <div class="skeleton h-7 w-48 mb-6"></div>
        <div class="flex space-x-6 overflow-x-auto pb-4 mb-8 store-scrollbar">
            @for($i = 0; $i < 5; $i++)
                <div class="w-[240px] shrink-0 border border-[#dedee5] rounded-xl p-5 flex flex-col items-center text-center">
                    {{-- Name Color Mockup --}}
                    <div class="h-16 flex items-center justify-center w-full">
                        <div class="skeleton h-6 w-32 rounded-lg"></div>
                    </div>

                    {{-- Item Name --}}
                    <div class="skeleton h-5 w-24 mt-2"></div>

                    {{-- Item Description (2 lines mockup) --}}
                    <div class="w-full mt-1 h-10 px-2 flex flex-col justify-center items-center space-y-1">
                        <div class="skeleton h-3 w-full"></div>
                        <div class="skeleton h-3 w-3/4"></div>
                    </div>

                    {{-- Purchase/Equip Button --}}
                    <div class="mt-4 w-full">
                        <div class="skeleton h-10 w-full rounded-[8px]"></div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Avatar Frames Section Mockup -->
        <div class="skeleton h-7 w-48 mb-6"></div>
        <div class="flex space-x-6 overflow-x-auto pb-4 store-scrollbar">
            @for($i = 0; $i < 4; $i++)
                <div class="w-[240px] shrink-0 border border-[#dedee5] rounded-xl p-5 flex flex-col items-center text-center">
                    {{-- Avatar Frame Mockup --}}
                    <div class="h-20 flex items-center justify-center mb-2 mt-4">
                        <div class="skeleton h-16 w-16 rounded-full"></div>
                    </div>

                    {{-- Item Name --}}
                    <div class="skeleton h-5 w-24 mt-2"></div>

                    {{-- Item Description (2 lines mockup) --}}
                    <div class="w-full mt-1 h-10 px-2 flex flex-col justify-center items-center space-y-1">
                        <div class="skeleton h-3 w-full"></div>
                        <div class="skeleton h-3 w-3/4"></div>
                    </div>

                    {{-- Purchase/Equip Button --}}
                    <div class="mt-4 w-full">
                        <div class="skeleton h-10 w-full rounded-[8px]"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
